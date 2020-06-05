<?php

namespace api\models;

use api\traits\DatabaseNameTrait;
use Yii;

/**
 * This is the model class for table "cc_org_area".
 *
 * @property int $id
 * @property int $org_id 组织id
 * @property int $area_id 4S店id
 * @property int $is_del 是否删除：0正常，1删除
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 * @property int $created_by
 * @property int $updated_by
 */
class OrgArea extends ActiveRecord
{
    use DatabaseNameTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'call_center.cc_ucenter_org_area';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['org_id', 'area_id', 'is_del', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'org_id' => '组织id',
            'area_id' => '4S店id',
            'is_del' => '是否删除：0正常，1删除',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'created_by' => '创建人员',
            'updated_by' => '更新人员',
        ];
    }

    /**
     * {@inheritdoc}
     * @return \api\models\query\OrgAreaQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \api\models\query\OrgAreaQuery(get_called_class());
    }
}
