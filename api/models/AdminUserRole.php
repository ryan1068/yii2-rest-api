<?php

namespace api\models;

use api\models\query\OrgAdminQuery;
use api\traits\DatabaseNameTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "cc_admin_user_role".
 *
 * @property int $id
 * @property int $admin_id 用户id
 * @property int $role_id 角色id
 * @property int $status 是否是当前使用角色（一个用户只有能一个当前使用的角色）：0否，1是
 * @property int $is_del 是否删除：0正常，1删除
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 * @property int $created_by
 * @property int $updated_by
 *
 * @property OrgAdmin[] $orgAdmins
 * @property Role[] $role
 */
class AdminUserRole extends ActiveRecord
{
    use DatabaseNameTrait;

    const STATUS_DISABLE = 0;   //角色停用

    const STATUS_ENABLE = 1;    //角色启用

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cc_ucenter_admin_user_role';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['admin_id', 'role_id', 'status', 'is_del', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'admin_id' => '用户id',
            'role_id' => '角色id',
            'status' => '是否是当前使用角色（一个用户只有能一个当前使用的角色）：0否，1是',
            'is_del' => '是否删除：0正常，1删除',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'created_by' => '创建人员',
            'updated_by' => '更新人员',
        ];
    }

    /**
     * {@inheritdoc}
     * @return \api\models\query\AdminUserRoleQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \api\models\query\AdminUserRoleQuery(get_called_class());
    }

    /**
     * @return bool
     */
    public function isEnable()
    {
        return $this->status == self::STATUS_ENABLE;
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
     * @return \yii\db\ActiveQuery|OrgAdminQuery
     */
    public function getOrgAdmins()
    {
        return $this->hasMany(OrgAdmin::class, ['role_id' => 'role_id'])
            ->onCondition([OrgAdmin::withDatabaseName('admin_id') => $this->admin_id]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(Role::class, ['id' => 'role_id']);
    }
}
