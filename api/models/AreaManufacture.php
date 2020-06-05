<?php

namespace api\models;

use api\traits\DatabaseNameTrait;

/**
 * This is the model class for table "cc_group_area_manufacture".
 *
 * @property int $id
 * @property int $area_id
 * @property int $cat_id
 * @property int $is_del 0:正常，1:删除
 * @property int $t_cycle 时间周期
 * @property int $m_cycle 里程周期
 * @property int $brand_id 品牌ID
 * @property string $brand 品牌名
 * @property string $manufacture 厂商品牌名
 * @property int $sort 排序字段
 */
class AreaManufacture extends \yii\db\ActiveRecord
{
    use DatabaseNameTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cc_group_area_manufacture';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['area_id', 'cat_id', 'is_del', 't_cycle', 'm_cycle', 'brand_id', 'sort'], 'integer'],
            [['brand', 'manufacture'], 'string', 'max' => 50],
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
            'cat_id' => 'Cat ID',
            'is_del' => '0:正常，1:删除',
            't_cycle' => '时间周期',
            'm_cycle' => '里程周期',
            'brand_id' => '品牌ID',
            'brand' => '品牌名',
            'manufacture' => '厂商品牌名',
            'sort' => '排序字段',
        ];
    }

    /**
     * {@inheritdoc}
     * @return \api\models\query\AreaManufactureQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \api\models\query\AreaManufactureQuery(get_called_class());
    }
}
