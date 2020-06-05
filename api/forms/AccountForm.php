<?php
/**
 * User: Ryan
 * Date: 2020/4/13
 * Time: 13:59
 */

namespace api\forms;

use api\models\ActiveRecord;
use api\models\AdminUserRole;
use api\models\OrgAdmin;
use api\models\query\AdminUserQuery;
use api\resources\AdminUser;
use api\resources\Role;
use api\traits\ArrayTrait;
use common\validators\ArrayValidator;
use common\validators\TelValidator;
use yii\base\Model;
use yii\base\UserException;
use yii\helpers\ArrayHelper;

/**
 * Class AccountForm
 * @package api\forms
 */
class AccountForm extends Model
{
    use ArrayTrait;

    public $accountId;
    public $name;
    public $account;
    public $password;
    public $tel;
    public $type;
    public $status;
    public $roleIds;
    public $nickname;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['name', 'status', 'roleIds', 'status', 'tel'], 'required', 'on' => [ActiveRecord::SCENARIO_UPDATE, ActiveRecord::SCENARIO_CREATE]],
            [['account'], 'required', 'on' => [ActiveRecord::SCENARIO_CREATE]],
            [['account'], function ($attribute) {
                if ($this->$attribute == 'admin' || $this->$attribute == 'administrator') {
                    $this->addError($attribute, '非法用户名');
                    return;
                }
            }],
            [['account'], 'unique', 'targetClass' => AdminUser::class,
                'filter' => function (AdminUserQuery $query) {
                    if ($this->scenario == ActiveRecord::SCENARIO_CREATE) {
                        return $query->active();
                    } else {
                        return $query->andWhere(['<>', 'id', $this->accountId])->active();
                    }
                },
                'on' => [ActiveRecord::SCENARIO_UPDATE, ActiveRecord::SCENARIO_CREATE],
                'message' => '该账号已存在，请重新填写账号',
            ],

            [['password'], 'required', 'on' => [ActiveRecord::SCENARIO_CREATE], 'when' => function () {
                return $this->type == AdminUser::TYPE_DEFINE;
            }],

            [['name', 'account', 'tel', 'password'], 'string'],
            [['name', 'account', 'tel', 'password'], 'trim'],
            [['tel'], TelValidator::class],
            [['name'], function ($attribute) {
                $this->nickname = $this->$attribute;
            }],

            [['password'], 'filter', 'filter' => function ($value) {
                if ($value) {
                    return \Yii::$app->security->generatePasswordHash($value);
                }
                return null;
            }],

            [['type', 'status'], 'integer'],
            [['type', 'status'], 'in', 'range' => [AdminUser::TYPE_DEFINE, AdminUser::TYPE_YECAI]],
            [['type', 'status'], 'default', 'value' => 0],

            [['accountId'], 'required', 'except' => ActiveRecord::SCENARIO_CREATE],
            [['accountId'], 'exist', 'targetAttribute' => 'id', 'targetClass' => AdminUser::class,
               'filter' => function (AdminUserQuery $query) {
                    return $query->active();
            }, 'except' => [ActiveRecord::SCENARIO_CREATE, ActiveRecord::SCENARIO_DELETE]],

            [['accountId'], 'string', 'on' => ActiveRecord::SCENARIO_DELETE],
            [['accountId'], 'trim', 'on' => ActiveRecord::SCENARIO_DELETE],
            [['accountId'], 'convertArray', 'on' => ActiveRecord::SCENARIO_DELETE],

            [['roleIds'], ArrayValidator::class],
            [['roleIds'], 'filter', 'filter' => function ($value) {
                return array_unique($value);
            }],
            [['roleIds'], 'each', 'rule' => [function ($attribute) {
                $role = Role::find()->id($this->$attribute)->active()->one();
                if (!$role) {
                    $this->addError($attribute, '角色id有误');
                    return;
                }
                if ($role->isAdministrator()) {
                    $this->addError($attribute, '不能添加超级管理员');
                    return;
                }
            }]],
        ];
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[ActiveRecord::SCENARIO_CREATE] = $this->removeAttributes(['accountId']);
        $scenarios[ActiveRecord::SCENARIO_UPDATE] = $this->removeAttributes(['account']);
        $scenarios[ActiveRecord::SCENARIO_VIEW] = ['accountId'];
        $scenarios[ActiveRecord::SCENARIO_DELETE] = ['accountId'];
        return $scenarios;
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'accountId',
            'name',
            'account',
            'tel',
            'password',
            'type',
            'status',
            'roleIds',
            'nickname',
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'accountId' => '账号id',
            'name' => '姓名',
            'account' => '账号',
            'tel' => '电话号码',
            'password' => '密码',
            'type' => '用户类型',
            'status' => '账号状态',
            'roleIds' => '角色',
        ];
    }

    /**
     * 创建账号
     * @return array
     * @throws UserException
     */
    public function create()
    {
        $tran = AdminUser::getDb()->beginTransaction();
        try {
            $auth = \Yii::$app->authManager;

            $adminUser = new AdminUser();
            $adminUser->setAttributes(isset($this->password)
                ? $this->getSafeAttributes()
                : $this->getSafeAttributes(['password']));
            if (!$adminUser->save()) {
                throw new UserException('创建账号失败');
            }

            foreach ($this->roleIds as $key => $roleId) {
                $adminUserRole = new AdminUserRole();
                $adminUserRole->admin_id = $adminUser->id;
                $adminUserRole->role_id = $roleId;
                if (!$adminUserRole->save()) {
                    throw new UserException('创建账号所属角色失败');
                }

                //设置第一个角色为用户默认角色
                if ($key == 0) {
                    $adminUser->setRoleId($roleId);
                    $adminUser->save();

                    $adminUserRole->setEnable();
                    $adminUserRole->save();

                    $role = Role::find()->id($roleId)->active()->one();
                    $assignment = $auth->assign($auth->getRole($role->role_key), $adminUser->id);
                    if (!$assignment) {
                        throw new UserException('创建账号所属rbac角色失败');
                    }
                }
            }

            $tran->commit();
        } catch (\Exception $e) {
            $tran->rollBack();
            throw new UserException($e->getMessage());
        }

        return [];
    }

    /**
     * 更新账号
     * @return array
     * @throws UserException
     * @throws \Throwable
     */
    public function update()
    {
        $tran = AdminUser::getDb()->beginTransaction();
        try {
            $adminUser = $this->getAdminUser();
            $adminUser->setAttributes(isset($this->password)
                ? $this->getSafeAttributes()
                : $this->getSafeAttributes(['password']));
            if (!$adminUser->save()) {
                throw new UserException('更新账号失败');
            }

            $adminUserRoles = $adminUser->getAdminUserRoles()->active()->all();
            $adminUserRoleIds = ArrayHelper::getColumn($adminUserRoles, 'role_id');

            //新增角色
            $addUserRoleIds = array_diff($this->roleIds, $adminUserRoleIds);
            foreach ($addUserRoleIds as $addRoleId) {
                $adminUserRole = new AdminUserRole();
                $adminUserRole->admin_id = $adminUser->id;
                $adminUserRole->role_id = $addRoleId;
                if (!$adminUserRole->save()) {
                    throw new UserException('创建账号所属角色失败');
                }
            }

            //删除角色
            $delUserRoleIds = array_diff($adminUserRoleIds, $this->roleIds);
            $delUserRoles = array_filter($adminUserRoles, function ($item) use ($delUserRoleIds) {
                return ArrayHelper::isIn($item['role_id'], $delUserRoleIds);
            });

            foreach ($delUserRoles as $userRole) {
                /* @var $userRole AdminUserRole */
                //如果用户当前角色被删除，则重置用户角色
                if ($userRole->role_id == $adminUser->currentRoleId) {
                    $adminUser->setRoleId(0);
                    $adminUser->save();

                    $adminUser->destroyToken();
                }
                
                if (!$userRole->delete()) {
                    throw new UserException('更新账号所属角色失败');
                }
            }

            //解绑管理范围
            OrgAdmin::deleteAll(
                ['admin_id' => $this->accountId, 'role_id' => $delUserRoleIds]
            );

            $tran->commit();
        } catch (\Exception $e) {
            $tran->rollBack();
            throw new UserException($e->getMessage());
        }

        return [];
    }

    /**
     * 删除账号
     * @return array
     * @throws UserException
     */
    public function delete()
    {
        $tran = AdminUser::getDb()->beginTransaction();
        try {
            $auth = \Yii::$app->authManager;

            $adminUsers = AdminUser::findAll($this->accountId);
            foreach ($adminUsers as $adminUser) {
                if ($adminUser->isAdministrator()) {
                    throw new UserException('管理员账号不能删除');
                }
                
                /* @var $adminUser AdminUser */
                $adminUser->is_del = AdminUser::IS_DEL_YES;
                if (!$adminUser->save()) {
                    throw new UserException('删除账号失败');
                }

                //解绑角色
                AdminUserRole::deleteAll(['admin_id' => $adminUser->id]);

                //解绑管理人员
                OrgAdmin::deleteAll(['admin_id' => $adminUser->id]);

                $auth->revokeAll($adminUser->id);

                $adminUser->destroyToken();
            }

            $tran->commit();
        } catch (\Exception $e) {
            $tran->rollBack();
            throw new UserException($e->getMessage());
        }

        return [];
    }

    /**
     * @return array
     * @throws UserException
     */
    public function enable()
    {
        $adminUser = $this->getAdminUser();
        $adminUser->setEnable();
        if (!$adminUser->save()) {
            throw new UserException('启用失败');
        }

        return [];
    }

    /**
     * @return array
     * @throws UserException
     */
    public function disable()
    {
        $adminUser = $this->getAdminUser();
        $adminUser->setDisable();
        if (!$adminUser->save()) {
            throw new UserException('禁用失败');
        }

        return [];
    }

    /**
     * @return AdminUser|array|null
     */
    public function getAdminUser()
    {
        $adminUser = AdminUser::find()
            ->id($this->accountId)
            ->active()
            ->one();

        return $adminUser;
    }

}