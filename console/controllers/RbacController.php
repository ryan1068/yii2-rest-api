<?php
namespace console\controllers;
use api\resources\AdminUser;
use api\resources\Column;
use api\resources\Role;
use api\rbac\UserGroupRule;
use yii\base\UserException;


/**
 * Class RbacController
 * @package console\controllers
 */
class RbacController extends \yii\console\Controller
{
    /**
     * 初始化rbac数据
     * @throws \yii\base\Exception
     */
    public function actionInit()
    {
        $tran = Role::getDb()->beginTransaction();
        try {

            $auth = \Yii::$app->authManager;
            $auth->removeAll();

            $this->initPermission();

            $this->initRole();

            $this->initUser();

            $tran->commit();
        } catch (\Exception $e) {
            $tran->rollBack();
            throw new UserException($e->getMessage());
        }

        return [];
    }

    /**
     * 初始化权限
     * @return bool
     * @throws \Exception
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
     * @throws \Exception
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
     * 初始化rbac 规则
     * @return array
     * @throws UserException
     */
    public function actionInitRule()
    {
        $tran = Role::getDb()->beginTransaction();
        try {
            $auth = \Yii::$app->authManager;

            $rule = new UserGroupRule;
            $auth->add($rule);

            $admin = $auth->getRole(Role::ROLE_ADMINISTRATOR);
            $admin->ruleName = $rule->name;
            $auth->update(Role::ROLE_ADMINISTRATOR, $admin);

            $tran->commit();
        } catch (\Exception $e) {
            $tran->rollBack();
            throw new UserException($e->getMessage());
        }

        return [];
    }

    /**
     * 重置指定用户rbac权限
     *
     * @param int $uid
     * @return array
     * @throws UserException
     */
    public function actionResetUser(int $uid)
    {
        $tran = Role::getDb()->beginTransaction();
        try {
            $auth = \Yii::$app->authManager;

            $user = AdminUser::find()
                ->active()
                ->id($uid)
                ->one();

            /* @var $user AdminUser */
            $userRole = $user->getCurrentRole();

            if (!$userRole || (isset($userRole) && $userRole->isAdministrator())) {
                throw new UserException('当前用户没有指定角色');
            }

            $role = $auth->getRole($userRole->role_key);
            $auth->assign($role, $user->id);

            $tran->commit();
        } catch (\Exception $e) {
            $tran->rollBack();
            throw new UserException($e->getMessage());
        }

        return [];
    }

    /**
     * 重置指定角色rbac权限
     *
     * @param $roleId
     * @return array
     * @throws UserException
     */
    public function actionResetRole($roleId)
    {
        $tran = Role::getDb()->beginTransaction();
        try {
            $auth = \Yii::$app->authManager;

            $role = Role::find()->active()->id($roleId)->one();
            if (!$role) {
                throw new UserException('未找到角色');
            }

            $auth->removeChildren($auth->getRole($role->role_key));

            $permissions = $role->getPermissions()->active()->all();

            foreach ($permissions as $permission) {
                $addChild = $auth->addChild(
                    $auth->getRole($role->role_key),
                    $auth->getPermission($permission->permission)
                );
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
     * 重置rbac缓存（手动改了rbac数据库才需要刷新，否则不用）
     * @return bool
     */
    public function actionRefreshCache()
    {
        $auth = \Yii::$app->authManager;
        $this->clearCache();
        $auth->loadFromCache();

        return true;
    }

    /**
     * @throws \Exception
     */
    public function actionUp()
    {
        $auth = \Yii::$app->authManager;

        $account = $auth->createPermission('system:account:account');
        $account->description = 'account module';
        $auth->add($account);

        $role = $auth->createPermission('system:account:role');
        $role->description = 'role module';
        $auth->add($role);

        $area = $auth->createPermission('system:account:area');
        $role->description = 'area module';
        $auth->add($area);

        $org = $auth->createPermission('system:account:org');
        $role->description = 'org module';
        $auth->add($org);

        $leader = $auth->createRole('group_leader');
        $auth->add($leader);
        $auth->addChild($leader, $account);
        $auth->addChild($leader, $role);
        $auth->addChild($leader, $area);
        $auth->addChild($leader, $org);

        $inviter = $auth->createRole('inviter');
        $auth->add($inviter);
        $auth->addChild($inviter, $account);
        $auth->addChild($inviter, $role);
        $auth->addChild($inviter, $area);
        $auth->addChild($inviter, $org);

        $auth->assign($inviter, 1);
    }

    /**
     * 打印cache数据
     */
    public function actionCache()
    {
        var_dump(\Yii::$app->cache->get(\Yii::$app->authManager->cacheKey)) ;
    }

    /**
     * @return bool
     */
    private function clearCache()
    {
        return \Yii::$app->cache->delete(\Yii::$app->authManager->cacheKey);
    }
}