<?php

use yii\db\Migration;
use api\models\Column;
use api\models\Role;
use api\models\RolePermission;
use api\resources\AdminUser;

/**
 * Class m200522_024018_init_role
 */
class m200522_024018_init_role extends Migration
{
    /**
     * @var string 管理员默认密码
     */
    protected $password = '';

    /**
     * @var array 系统默认角色
     */
    protected static $roles = [
        [
            'name' => '超级管理员',
            'desc' => '超级管理员',
            'sort' => 0,
            'role_key' => Role::ROLE_ADMINISTRATOR,
        ],
        [
            'name' => 'DCC主管',
            'desc' => 'DCC主管',
            'sort' => 1,
            'role_key' => Role::ROLE_DCC_SUPERVISOR,
        ],
        [
            'name' => 'DCC组长',
            'desc' => 'DCC组长',
            'sort' => 2,
            'role_key' => Role::ROLE_DCC_GROUP_LEADER,
        ],
        [
            'name' => 'DCC邀约员',
            'desc' => 'DCC邀约员',
            'sort' => 3,
            'role_key' => Role::ROLE_DCC_INVITER,
        ],

    ];

    /**
     * @return bool
     * @throws \yii\base\Exception
     */
    public function safeUp()
    {
        $this->initRole();

        $this->initPermission();

        $this->initUser();

        return true;
    }

    /**
     * 初始化角色
     * @return bool
     */
    public function initRole()
    {
        foreach (self::$roles as $role) {
            if (Role::find()->roleKey($role['role_key'])->active()->exists()) {
                continue;
            }
            $roleModel = new Role();
            $roleModel->name = $role['name'];
            $roleModel->desc = $role['desc'];
            $roleModel->role_key = $role['role_key'];
            $roleModel->sort = $role['sort'];
            $roleModel->save();
        }

        return true;
    }

    /**
     * 初始化权限
     * @return bool
     */
    public function initPermission()
    {
        $roles = Role::findAll([
            'role_key' => [
                Role::ROLE_ADMINISTRATOR,
                Role::ROLE_DCC_INVITER,
                Role::ROLE_DCC_GROUP_LEADER,
                Role::ROLE_DCC_SUPERVISOR,
            ]
        ]);

        foreach ($roles as $role) {
            if (RolePermission::find()->andWhere(['role_id' => $role->id])->active()->exists()) {
                continue;
            }
            
            $rolePermission = new RolePermission();

            if ($role->isDccInviter()) {
                $menu = Column::findOne(['permission' => 'system:work:inviter']);
                $rolePermission->role_id = $role->id;
                $rolePermission->column_id = $menu->id;
                $rolePermission->save();
            }

            if ($role->isDccGroupLeader()) {
                $menu = Column::findOne(['permission' => 'system:work:leader']);
                $rolePermission->role_id = $role->id;
                $rolePermission->column_id = $menu->id;
                $rolePermission->save();
            }

            if ($role->isDccSupervisor()) {
                $menu = Column::findOne(['permission' => 'system:work:supervisor']);
                $rolePermission->role_id = $role->id;
                $rolePermission->column_id = $menu->id;
                $rolePermission->save();
            }
        }

        return true;
    }

    /**
     * 初始化用户
     * @return bool
     * @throws \yii\base\Exception
     */
    public function initUser()
    {
        if (AdminUser::find()->andWhere(['account' => 'admin'])->active()->exists()) {
            return false;
        }
        $adminRole = Role::find()->roleKey(Role::ROLE_ADMINISTRATOR)->active()->one();

        $admin = new AdminUser();
        $admin->account = 'admin';
        $admin->nickname = 'admin';
        $admin->current_role_id = $adminRole->id;
        $admin->setPassword($this->password);
        $admin->save();

        $userRole = new \api\models\AdminUserRole();
        $userRole->admin_id = $admin->id;
        $userRole->role_id = $adminRole->id;
        $userRole->setEnable();
        $userRole->save();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200522_024018_init_role cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200522_024018_init_role cannot be reverted.\n";

        return false;
    }
    */
}
