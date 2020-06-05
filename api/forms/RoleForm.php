<?php
/**
 * User: Ryan
 * Date: 2020/4/13
 * Time: 13:59
 */

namespace api\forms;

use api\models\ActiveRecord;
use api\models\AdminUserRole;
use api\models\Column;
use api\models\RolePermission;
use api\resources\Role;
use api\traits\ArrayTrait;
use common\validators\ArrayValidator;
use yii\base\Model;
use yii\base\UserException;
use yii\helpers\ArrayHelper;

/**
 * Class RoleForm
 * @package api\forms
 */
class RoleForm extends Model
{
    use ArrayTrait;

    public $name;
    public $desc;
    public $permissionIds;
    public $roleId;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['name', 'permissionIds'], 'required', 'on' => [ActiveRecord::SCENARIO_UPDATE, ActiveRecord::SCENARIO_CREATE]],
            [['desc'], 'string'],
            [['desc'], 'default', 'value' => ''],
            [['permissionIds'], ArrayValidator::class],

            [['roleId'], 'required', 'except' => ActiveRecord::SCENARIO_CREATE],
            [['roleId'], 'integer', 'on' => [ActiveRecord::SCENARIO_VIEW, ActiveRecord::SCENARIO_UPDATE]],

            [['roleId'], 'string', 'on' => ActiveRecord::SCENARIO_DELETE],
            [['roleId'], 'trim', 'on' => ActiveRecord::SCENARIO_DELETE],
            [['roleId'], 'convertArray', 'on' => ActiveRecord::SCENARIO_DELETE],
        ];
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[ActiveRecord::SCENARIO_CREATE] = ['name', 'desc', 'permissionIds'];
        $scenarios[ActiveRecord::SCENARIO_UPDATE] = ['roleId', 'name', 'desc', 'permissionIds'];
        $scenarios[ActiveRecord::SCENARIO_VIEW] = ['roleId'];
        $scenarios[ActiveRecord::SCENARIO_DELETE] = ['roleId'];
        return $scenarios;
    }

    /**
     * 创建角色
     * @return array
     * @throws UserException
     */
    public function create()
    {
        $tran = Role::getDb()->beginTransaction();
        try {
            $roleKey = \Yii::$app->security->generateRandomString(10);
            if (Role::find()->roleKey($roleKey)->active()->exists()) {
                throw new UserException('创建rbac角色失败');
            }

            $role = new Role();
            $role->name = $this->name;
            $role->desc = $this->desc;
            $role->role_key = $roleKey;
            $role->type = Role::TYPE_DEFINE;
            if (!$role->save()) {
                throw new UserException('创建角色失败');
            }

            $auth = \Yii::$app->authManager;
            $rbacRole = $auth->createRole($roleKey);
            $auth->add($rbacRole);

            foreach ($this->permissionIds as $permissionId) {
                $rolePermission = new RolePermission();
                $rolePermission->role_id = $role->id;
                $rolePermission->column_id = $permissionId;

                $permission = Column::findOne($permissionId);
                $addChild = $auth->addChild($rbacRole, $auth->getPermission($permission->permission));

                if (!$rolePermission->save() || !$addChild) {
                    throw new UserException('创建角色权限失败');
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
     * 更新角色
     * @return array
     * @throws UserException
     * @throws \Throwable
     */
    public function update()
    {
        $tran = Role::getDb()->beginTransaction();
        try {
            $auth = \Yii::$app->authManager;

            $role = $this->getRole();
            $role->name = $this->name;
            $role->desc = $this->desc;
            if (!$role->save()) {
                throw new UserException('更新角色失败');
            }

            // 添加权限
            $rolePermissions = $role->getRolePermissions()->active()->all();
            $rolePermissionIds = ArrayHelper::getColumn($rolePermissions, 'column_id');

            $addRolePermissionIds = array_diff($this->permissionIds, $rolePermissionIds);
            foreach ($addRolePermissionIds as $permissionId) {
                $rolePermission = new RolePermission();
                $rolePermission->role_id = $this->roleId;
                $rolePermission->column_id = $permissionId;
                if (!$rolePermission->save()) {
                    throw new UserException('更新角色权限失败');
                }
            }

            // 删除权限
            $delRolePermissionIds = array_diff($rolePermissionIds, $this->permissionIds);
            $delRolePermissions = array_filter($rolePermissions, function ($item) use ($delRolePermissionIds) {
                return ArrayHelper::isIn($item['column_id'], $delRolePermissionIds);
            });

            foreach ($delRolePermissions as $rolePermission) {
                /* @var $rolePermission RolePermission */
                if (!$rolePermission->delete()) {
                    throw new UserException('更新角色权限失败');
                }
            }

            // 重置rbac角色所属权限
            $auth->removeChildren($auth->getRole($role->role_key));
            foreach ($this->permissionIds as $permissionId) {
                $permission = Column::findOne($permissionId);
                if (!$permission) {
                    continue;
                }
                $addChild = $auth->addChild($auth->getRole($role->role_key), $auth->getPermission($permission->permission));
                if (!$addChild) {
                    throw new UserException('更新rbac角色权限失败');
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
     * 删除角色
     * @return array
     * @throws UserException
     */
    public function delete()
    {
        $tran = Role::getDb()->beginTransaction();
        try {
            $auth = \Yii::$app->authManager;

            $roles = Role::findAll($this->roleId);
            foreach ($roles as $role) {
                if ($role->type == Role::TYPE_SYSTEM) {
                    throw new UserException('系统角色不能删除');
                }

                /* @var $role Role */
                if ($role->getAdminUserRoles()->active()->exists()) {
                    throw new UserException('如需删除该角色请将角色下的用户全部移除');
                }

                $role->is_del = Role::IS_DEL_YES;
                if (!$role->save()) {
                    throw new UserException('删除角色失败');
                }

                RolePermission::deleteAll(['role_id' => $this->roleId]);

                AdminUserRole::deleteAll(['role_id' => $this->roleId]);

                // 清空rbac角色和所属权限
                if (
                    !$auth->removeChildren($auth->getRole($role->role_key))
                    || !$auth->remove($auth->getRole($role->role_key))
                ) {
                    throw new UserException('删除rbac角色失败');
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
     * @return \api\models\Role|array|null
     * @throws UserException
     */
    public function getRole()
    {
        $role = Role::find()->id($this->roleId)->active()->one();
        if (!$role) {
            throw new UserException('未找到角色');
        }
        return $role;
    }
}