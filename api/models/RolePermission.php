<?php

namespace api\models;

use api\traits\DatabaseNameTrait;
use Yii;

/**
 * This is the model class for table "cc_role_permission".
 *
 * @property int $id
 * @property int $role_id 角色id
 * @property int $column_id 权限菜单id
 * @property int $is_del 是否删除
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 * @property int $created_by
 * @property int $updated_by
 */
class RolePermission extends ActiveRecord
{
    use DatabaseNameTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cc_ucenter_role_permission';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['role_id', 'column_id', 'is_del', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'role_id' => '角色id',
            'column_id' => '权限菜单id',
            'is_del' => '是否删除',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'created_by' => '创建人员',
            'updated_by' => '更新人员',
        ];
    }

    /**
     * {@inheritdoc}
     * @return \api\models\query\RolePermissionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \api\models\query\RolePermissionQuery(get_called_class());
    }
}
