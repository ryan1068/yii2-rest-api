<?php

namespace api\models;

use api\models\query\AdminUserRoleQuery;
use api\models\query\OrgAdminQuery;
use api\models\query\OrgQuery;
use api\models\query\RoleQuery;
use api\traits\DatabaseNameTrait;
use api\resources\Org;
use api\resources\Role;

/**
 * This is the model class for table "cc_admin_user".
 *
 * @property int $id
 * @property string $account 账号
 * @property string $nickname 姓名
 * @property string $tel 电话
 * @property string $password 密码
 * @property int $status 账号状态:0启用，1停用
 * @property int $type 用户类型:0自定义增加，1业财增加
 * @property int $is_del 是否删除：0正常，1删除
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 * @property string $avatar 头像
 * @property string $email  邮箱
 * @property string $auth_key 认证key
 * @property int $current_role_id 当前的角色id
 * @property int $created_by
 * @property int $updated_by
 */
class AdminUser extends ActiveRecord
{
    use DatabaseNameTrait;

    const TYPE_DEFINE = 0;  //自定义增加

    const TYPE_YECAI = 1;   //业财增加

    const STATUS_ENABLE = 0;    //启用

    const STATUS_DISABLE = 1;   //禁用

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cc_ucenter_admin_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'type', 'is_del', 'created_at', 'updated_at', 'created_by', 'updated_by', 'current_role_id'], 'integer'],
            [['account', 'nickname'], 'string', 'max' => 15],
            [['tel'], 'string', 'max' => 11],
            [['password', 'auth_key'], 'string', 'max' => 100],
            [['avatar'], 'string', 'max' => 50],
            [['email'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'account' => '账号',
            'nickname' => '姓名',
            'tel' => '电话',
            'password' => '密码',
            'status' => '账号状态:0启用，1停用',
            'type' => '用户类型:0自定义增加，1业财增加',
            'is_del' => '是否删除：0正常，1删除',
            'avatar' => '头像',
            'email' => '邮箱',
            'auth_key' => '认证key',
            'current_role_id' => '当前的角色id',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'created_by' => '创建人员',
            'updated_by' => '更新人员',
        ];
    }

    /**
     * {@inheritdoc}
     * @return \api\models\query\AdminUserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \api\models\query\AdminUserQuery(get_called_class());
    }

    /**
     * set status enable
     */
    public function setEnable()
    {
        $this->status = self::STATUS_ENABLE;
    }

    /**
     * set status disable
     */
    public function setDisable()
    {
        $this->status = self::STATUS_DISABLE;
    }

    /**
     * @return bool
     */
    public function isEnable()
    {
        return $this->status == self::STATUS_ENABLE;
    }

    /**
     * @return bool
     */
    public function isYecai()
    {
        return $this->type == self::TYPE_YECAI;
    }

    /**
     * @param $roleId
     */
    public function setRoleId($roleId)
    {
        $this->current_role_id = $roleId;
    }

    /**
     * @return \yii\db\ActiveQuery|OrgAdminQuery
     */
    public function getOrgAdmins()
    {
        return $this->hasMany(OrgAdmin::class, ['admin_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery|AdminUserRoleQuery
     */
    public function getAdminUserRoles()
    {
        return $this->hasMany(AdminUserRole::class, ['admin_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery|OrgQuery
     */
    public function getOrgs()
    {
        return $this->hasMany(Org::class, ['id' => 'org_id'])->via('orgAdmins');
    }

    /**
     * @return \yii\db\ActiveQuery|RoleQuery
     */
    public function getRoles()
    {
        return $this->hasMany(Role::class, ['id' => 'role_id'])->via('adminUserRoles');
    }
}
