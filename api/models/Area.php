<?php

namespace api\models;

use api\models\query\AreaAdminUserQuery;
use api\models\query\AreaManufactureQuery;
use api\models\query\OrgQuery;
use api\resources\AreaManufacture;
use api\traits\DatabaseNameTrait;
use api\resources\Org;

/**
 * This is the model class for table "cc_group_area".
 *
 * @property int $id
 * @property int $cst_area_id 关联车商通店id
 * @property string $full_name 完整名称
 * @property string $short_name 完整名称
 * @property string $address 详细地址
 * @property string $caloai_id 高科应用id
 * @property string $caloai_key 高科应用密钥
 * @property int $is_del 是否已删除，0否，1是
 * @property int $created_at 创建时间
 * @property int $created_by 创建人
 * @property int $updated_at 更新日期
 * @property int $updated_by 更新人
 */
class Area extends ActiveRecord
{
    use DatabaseNameTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cc_group_area';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['is_del', 'created_at', 'created_by', 'updated_at', 'updated_by', 'cst_area_id'], 'integer'],
            [['full_name', 'short_name'], 'string', 'max' => 64],
            [['address'], 'string', 'max' => 255],
            [['caloai_id', 'caloai_key'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cst_area_id' => '关联车商通店id',
            'full_name' => '完整名称',
            'short_name' => '完整名称',
            'address' => '详细地址',
            'caloai_id' => '高科应用id',
            'caloai_key' => '高科应用密钥',
            'is_del' => '是否已删除，0否，1是',
            'created_at' => '创建时间',
            'created_by' => '创建人',
            'updated_at' => '更新日期',
            'updated_by' => '更新人',
        ];
    }

    /**
     * {@inheritdoc}
     * @return \api\models\query\AreaQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \api\models\query\AreaQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrgAreas()
    {
        return $this->hasMany(OrgArea::class, ['area_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery|OrgQuery
     */
    public function getOrgs()
    {
        return $this->hasMany(Org::class, ['id' => 'org_id'])->via('orgAreas');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrgAdmins()
    {
        return $this->hasMany(OrgAdmin::class, ['org_id' => 'id'])->via('orgs');
    }

    /**
     * @return \yii\db\ActiveQuery|AreaManufactureQuery
     */
    public function getAreaManufactures()
    {
        return $this->hasMany(AreaManufacture::class, ['area_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery|AreaAdminUserQuery
     */
    public function getAreaAdminUser()
    {
        return $this->hasMany(AreaAdminUser::class, ['area_id' => 'id']);
    }
}
