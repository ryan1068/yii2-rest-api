<?php
namespace api\forms;

use api\models\AdminUserRole;
use api\models\query\RoleQuery;
use api\resources\AdminUser;
use api\resources\Role;
use Yii;
use yii\base\Model;
use yii\base\UserException;

/**
 * Class SessionForm
 * @package api\forms
 */
class SessionForm extends Model
{
    public $account;
    public $password;
    public $roleId;

    /**
     * @var AdminUser
     */
    private $_user;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // account and password are both required
            [['account', 'password'], 'required', 'on' => ['login']],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],

            [['roleId'], 'required', 'on' => ['role']],
            [['roleId'], 'integer'],
            [['roleId'], 'exist', 'targetAttribute' => 'id', 'targetClass' => Role::class,
                'filter' => function ($query) {
                    /* @var $query RoleQuery */
                    return $query->active();
            }],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['login'] = ['account', 'password'];
        $scenarios['role'] = ['roleId'];
        return $scenarios;
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     * @throws \yii\base\InvalidConfigException
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user) {
                $this->addError($attribute, '用户名或密码错误');
                return;
            }

            if (!$user->isYecai()) {
                if (!$user->validatePassword($this->password)) {
                    $this->addError($attribute, '用户名或密码错误');
                    return;
                }
                if (!$user->isEnable()) {
                    $this->addError($attribute, '用户已被停用');
                    return;
                }
            } else {
                $this->validateYecai($attribute, $params);
                return;
            }
        }
    }

    /**
     * @param $attribute
     * @param $params
     * @throws \yii\base\InvalidConfigException
     */
    public function validateYecai($attribute, $params)
    {
        $response = Yii::$app->yecai->auth($this->account, $this->password);
        if (isset($response) && !$response['access_token']) {
            $this->addError($attribute, '业财账号登录失败');
        }

        return;
    }

    /**
     * 设置当前用户角色
     * @param AdminUser $admin
     * @return array
     * @throws UserException
     */
    public function update(AdminUser $admin)
    {
        $tran = AdminUser::getDb()->beginTransaction();
        try {
            $userRole = $admin
                ->getAdminUserRoles()
                ->roleId($this->roleId)
                ->active()
                ->one();

            if (!$userRole) {
                throw new UserException("未找到用户所属的角色");
            }

            if ($userRole->isEnable()) {
                return [];
            }

            $userRole->setEnable();
            $admin->current_role_id = $this->roleId;
            if (!$userRole->save() ||!$admin->save()) {
                throw new UserException("设置用户当前角色失败");
            }

            AdminUserRole::updateAll(
                ['status' => AdminUserRole::STATUS_DISABLE],
                ['AND', ['<>', 'role_id', $this->roleId], ['admin_id' => $admin->id]]
            );

            // 更新用户rbac权限
            $auth = \Yii::$app->authManager;
            $auth->revokeAll($admin->id);

            $role = Role::find()->id($this->roleId)->active()->one();
            $auth->assign($auth->getRole($role->role_key), $admin->id);

            $tran->commit();
        } catch (\Exception $e) {
            $tran->rollBack();
            throw new UserException($e->getMessage());
        }

        return [];
    }

    /**
     * Logs in a user using the provided account and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser());
        }

        return false;
    }

    /**
     * Finds user by [[account]]
     *
     * @return AdminUser|null
     */
    public function getUser()
    {
        if ($this->_user === null) {
            $this->_user = AdminUser::findOne(['account' => $this->account, 'is_del' => 0]);
        }

        return $this->_user;
    }
}
