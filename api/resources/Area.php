<?php
/**
 * User: Ryan
 * Date: 2020/4/13
 * Time: 17:35
 */

namespace api\resources;

use yii\helpers\ArrayHelper;


/**
 * Class Area
 * @package api\resources
 */
class Area extends \api\models\Area
{
    /**
     * @return array
     */
    public function fields()
    {
        return [
            'id',
            'cst_area_id',
            'short_name',
            'full_name',
            'address',
            'caloai_id',
            'caloai_key',
            'admin_name' => function () {
                $admin =  $this->getAreaAdminUser()
                    ->active()
                    ->one();
                return ArrayHelper::getValue($admin, 'name', '');
            },
            'created_date' => function () {
                return date('Y-m-d H:i', $this->created_at);
            },
        ];
    }

    /**
     * @return array
     */
    public function extraFields()
    {
        return [
            'brands' => function () {
                return $this->getAllManufactures();
            },
            'orgs' => function () {
                return $this->getAllOrgs();
            },
        ];
    }

    /**
     * 获取所有厂商
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getAllManufactures()
    {
        return $this->getAreaManufactures()
            ->active()
            ->all();
    }

    /**
     * 获取所有组织
     * @return \api\models\Org[]|array
     */
    public function getAllOrgs()
    {
        return $this->getOrgs()
            ->active()
            ->all();
    }
}