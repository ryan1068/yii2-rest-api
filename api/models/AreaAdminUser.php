<?php

namespace api\models;

use api\traits\DatabaseNameTrait;
use Yii;

/**
 * This is the model class for table "cc_group_area_admin_user".
 *
 * @property int $id
 * @property int $area_id
 * @property string $name
 * @property string $tel
 * @property int $is_del 是否已删除，0否，1是
 * @property int $created_at 创建时间
 * @property int $created_by 创建人
 * @property int $updated_at 更新日期
 * @property int $updated_by 更新人
 */
class AreaAdminUser extends \yii\db\ActiveRecord
{
    use DatabaseNameTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cc_group_area_admin_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['area_id', 'is_del', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['name'], 'string', 'max' => 30],
            [['tel'], 'string', 'max' => 11],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'area_id' => 'Area ID',
            'name' => 'Name',
            'tel' => 'Tel',
            'is_del' => '是否已删除，0否，1是',
            'created_at' => '创建时间',
            'created_by' => '创建人',
            'updated_at' => '更新日期',
            'updated_by' => '更新人',
        ];
    }

    /**
     * {@inheritdoc}
     * @return \api\models\query\AreaAdminUserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \api\models\query\AreaAdminUserQuery(get_called_class());
    }
}
