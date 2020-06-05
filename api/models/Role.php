<?php

namespace api\models;

use api\models\query\AdminUserQuery;
use api\models\query\AdminUserRoleQuery;
use api\models\query\ColumnQuery;
use api\models\query\RolePermissionQuery;
use api\traits\DatabaseNameTrait;
use api\resources\AdminUser;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "cc_role".
 *
 * @property int $id
 * @property int $pid 上级id
 * @property string $name 角色名称
 * @property int $type 角色类型：0系统角色，1自定义角色
 * @property string $desc 角色描述
 * @property string $role_key 标识
 * @property int $is_del 是否删除：0正常，1删除
 * @property int $sort 排序
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 * @property int $created_by
 * @property int $updated_by
 *
 * @property RolePermission[] $rolePermissions
 * @property AdminUserRole[] $adminUserRoles
 */
class Role extends ActiveRecord
{
    use DatabaseNameTrait;

    const TYPE_SYSTEM = 0;  //系统角色

    const TYPE_DEFINE = 1;  //自定义角色

    const ROLE_ADMINISTRATOR = 'administrator';   //超级管理员

    const ROLE_DCC_INVITER = 'dcc_inviter';     //DCC邀约员

    const ROLE_DCC_GROUP_LEADER = 'dcc_group_leader';    //DCC组长

    const ROLE_DCC_SUPERVISOR = 'dcc_supervisor';  //DCC主管

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cc_ucenter_role';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pid', 'type', 'is_del', 'created_at', 'updated_at', 'created_by', 'updated_by', 'sort'], 'integer'],
            [['name'], 'string', 'max' => 20],
            [['role_key'], 'string', 'max' => 50],
            [['desc'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pid' => '上级id',
            'name' => '角色名称',
            'type' => '角色类型：0系统角色，1自定义角色',
            'desc' => '角色描述',
            'role_key' => '角色标识',
            'sort' => '排序',
            'is_del' => '是否删除：0正常，1删除',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'created_by' => '创建人员',
            'updated_by' => '更新人员',
        ];
    }

    /**
     * {@inheritdoc}
     * @return \api\models\query\RoleQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \api\models\query\RoleQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery|RolePermissionQuery
     */
    public function getRolePermissions()
    {
        return $this->hasMany(RolePermission::class, ['role_id' => 'id']);
    }

    /**
     * 获取权限
     * @return \yii\db\ActiveQuery|ColumnQuery
     */
    public function getPermissions()
    {
        return $this->hasMany(Column::class, ['id' => 'column_id'])->via('rolePermissions');
    }

    /**
     * @return \yii\db\ActiveQuery|AdminUserRoleQuery
     */
    public function getAdminUserRoles()
    {
        return $this->hasMany(AdminUserRole::class, ['role_id' => 'id']);
    }

    /**
     * 获取用户
     * @return \yii\db\ActiveQuery|AdminUserQuery
     */
    public function getAdminUsers()
    {
        return $this->hasMany(AdminUser::class, ['id' => 'admin_id'])->via('adminUserRoles');
    }

    /**
     * @return bool
     */
    public function isAdministrator()
    {
        return $this->role_key == self::ROLE_ADMINISTRATOR;
    }

    /**
     * 获取dcc角色标识
     * @return array
     */
    public static function getDccRoleKey()
    {
        return [
            self::ROLE_DCC_INVITER,
            self::ROLE_DCC_GROUP_LEADER,
            self::ROLE_DCC_SUPERVISOR,
        ];
    }

    /**
     * @return bool
     */
    public function isDcc()
    {
        return ArrayHelper::isIn($this->role_key, $this->getDccRoleKey());
    }

    /**
     * @return bool
     */
    public function isDccInviter()
    {
        return $this->role_key == self::ROLE_DCC_INVITER;
    }

    /**
     * @return bool
     */
    public function isDccGroupLeader()
    {
        return $this->role_key == self::ROLE_DCC_GROUP_LEADER;
    }

    /**
     * @return bool
     */
    public function isDccSupervisor()
    {
        return $this->role_key == self::ROLE_DCC_SUPERVISOR;
    }
}
