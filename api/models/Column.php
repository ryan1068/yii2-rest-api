<?php

namespace api\models;

use api\traits\DatabaseNameTrait;
use Yii;

/**
 * This is the model class for table "cc_column".
 *
 * @property int $id
 * @property int $pid 上级id
 * @property string $name 菜单名称
 * @property string $url 菜单地址
 * @property string $permission 菜单权限标识
 * @property string $remark 菜单详情
 * @property int $sort 菜单排序
 * @property int $is_del 是否删除：0正常，1删除
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 * @property int $visible
 * @property string $icon
 * @property int $created_by
 * @property int $updated_by
 */
class Column extends ActiveRecord
{
    use DatabaseNameTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cc_ucenter_column';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pid', 'is_del', 'created_at', 'updated_at', 'visible', 'created_by', 'updated_by', 'sort'], 'integer'],
            [['url', 'name'], 'string', 'max' => 50],
            [['visible'], 'string', 'max' => 255],
            [['remark', 'permission'], 'string', 'max' => 100],
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
            'name' => '菜单名称',
            'url' => '菜单地址',
            'permission' => '菜单权限标识',
            'sort' => '排序',
            'remark' => '菜单详情',
            'icon' => '图标',
            'visible' => '是否显示：0显示，1不显示',
            'is_del' => '是否删除：0正常，1删除',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'created_by' => '创建人员',
            'updated_by' => '更新人员',
        ];
    }

    /**
     * {@inheritdoc}
     * @return \api\models\query\ColumnQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \api\models\query\ColumnQuery(get_called_class());
    }
}
