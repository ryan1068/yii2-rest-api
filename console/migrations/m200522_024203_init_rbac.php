<?php

use yii\db\Migration;
use api\rbac\UserGroupRule;
use api\resources\AdminUser;
use api\resources\Column;
use api\resources\Role;
use yii\base\UserException;

/**
 * Class m200522_024203_init_rbac
 */
class m200522_024203_init_rbac extends Migration
{
    /**
     * @return array|bool
     * @throws UserException
     * @throws \yii\base\Exception
     * @throws \Exception
     */
    public function safeUp()
    {
        $auth = \Yii::$app->authManager;
        $auth->removeAll();

        $this->initPermission();

        $this->initRole();

        $this->initUser();

        return [];
    }

    /**
     * 初始化权限
     * @return bool
     * @throws Exception
     */
    public function initPermission()
    {
        $auth = \Yii::$app->authManager;

        $permissions = Column::find()
            ->active()
            ->all();

        foreach ($permissions as $permission) {
            $rbacPermission = $auth->createPermission($permission->permission);
            $rbacPermission->description = $permission->remark;
            $auth->add($rbacPermission);
        }

        return true;
    }

    /**
     * 初始化角色
     * @return bool
     * @throws UserException
     * @throws \yii\base\Exception
     * @throws \Exception
     */
    public function initRole()
    {
        $auth = \Yii::$app->authManager;

        $roles = Role::find()
            ->active()
            ->all();

        foreach ($roles as $role) {
            $rbacRole = $auth->createRole($role->role_key);
            $rbacRole->description = $role->desc;
            $auth->add($rbacRole);

            // 增加rbac规则
            if ($role->isAdministrator() || $role->isDcc()) {
                $rule = new UserGroupRule;
                if (!$auth->getRule($rule->name)) {
                    $auth->add($rule);
                }

                $rbacRole->ruleName = $rule->name;
                $auth->update($role->role_key, $rbacRole);
            }

            // 增加权限
            // foreach ($permissions as $permission) {
            //     if ($role->isAdministrator()) {
            //         continue;
            //     }

            //     if ($role->isDccInviter() && $permission->permission == 'system:work:inviter') {
            //         $rbacPermission = $auth->getPermission('system:work:inviter');
            //         $auth->addChild($rbacRole, $rbacPermission);
            //     }

            //     if ($role->isDccGroupLeader() && $permission->permission == 'system:work:leader') {
            //         $rbacPermission = $auth->getPermission('system:work:leader');
            //         $auth->addChild($rbacRole, $rbacPermission);
            //     }

            //     if ($role->isDccSupervisor() && $permission->permission == 'system:work:supervisor') {
            //         $rbacPermission = $auth->getPermission('system:work:supervisor');
            //         $auth->addChild($rbacRole, $rbacPermission);
            //     }

            //     // $rbacPermission = $auth->getPermission($permission->permission);
            //     // $auth->addChild($rbacRole, $rbacPermission);
            // }

            // 角色赋值权限
            $permissions = $role
                ->getPermissions()
                ->active()
                ->all();

            foreach ($permissions as $permission) {
                $addChild = $auth->addChild(
                    $auth->getRole($role->role_key),
                    $auth->getPermission($permission->permission)
                );
                if (!$addChild) {
                    throw new UserException('更新rbac角色权限失败');
                }
            }
        }

        return true;
    }
    
    /**
     * 初始化用户
     * @throws Exception
     */
    public function initUser()
    {
        $auth = \Yii::$app->authManager;

        $users = AdminUser::find()
            ->active()
            ->all();

        foreach ($users as $user) {
            /* @var $user AdminUser */
            $userRole = $user->getCurrentRole();

            if (!$userRole || (isset($userRole) && $userRole->isAdministrator())) {
                continue;
            }

            $role = $auth->getRole($userRole->role_key);
            $auth->assign($role, $user->id);
        }

        return true;
    }
    
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200522_024203_init_rbac cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200522_024203_init_rbac cannot be reverted.\n";

        return false;
    }
    */
}
