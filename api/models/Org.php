<?php

namespace api\models;

use api\models\query\AreaQuery;
use api\models\query\OrgAreaQuery;
use api\models\query\OrgQuery;
use api\traits\DatabaseNameTrait;
use Yii;
use api\resources\Area;
use api\resources\AdminUser;

/**
 * This is the model class for table "cc_org".
 *
 * @property int $id
 * @property int $pid 上级id
 * @property string $name 组织名称
 * @property int $is_del 是否删除：0正常，1删除
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 * @property int $created_by
 * @property int $updated_by
 * @property int $sort
 *
 * @property OrgArea[] $orgAreas
 */
class Org extends ActiveRecord
{
    use DatabaseNameTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'call_center.cc_ucenter_org';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pid', 'is_del', 'created_at', 'updated_at', 'created_by', 'updated_by', 'sort'], 'integer'],
            [['name'], 'string', 'max' => 20],
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
            'name' => '组织名称',
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
     * @return \api\models\query\OrgQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \api\models\query\OrgQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery|OrgAreaQuery
     */
    public function getOrgAreas()
    {
        return $this->hasMany(OrgArea::class, ['org_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery|AreaQuery
     */
    public function getAreas()
    {
        return $this->hasMany(Area::class, ['id' => 'area_id'])->via('orgAreas');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrgAdmins()
    {
        return $this->hasMany(OrgAdmin::class, ['org_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdmins()
    {
        return $this->hasMany(AdminUser::class, ['id' => 'admin_id'])->via('orgAdmins');
    }

    /**
     * @return \yii\db\ActiveQuery|OrgQuery
     */
    public function getChildren()
    {
        return $this->hasMany(Org::class, ['pid' => 'id']);
    }
}
