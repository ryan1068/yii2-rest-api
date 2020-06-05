<?php

namespace api\resources;


use api\models\AdminUserRole;
use api\services\AccountService;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\IdentityInterface;

/**
 * Class AreaAdminUser
 * @package api\resources
 */
class AreaAdminUser extends \api\models\AreaAdminUser
{
    /**
     * @return array
     */
    public function fields()
    {
        return [
            'id' => 'au_id',
            'account' => 'au_name',
            'nickname',
            'tel' => 'au_Tel',
            'created_date' => function () {
                return date('Y-m-d H:i', $this->addtime);
            },
        ];
    }
}