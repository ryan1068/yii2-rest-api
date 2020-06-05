<?php

namespace api\traits;
use api\models\Area;
use api\resources\AdminUser;
use yii\helpers\ArrayHelper;

/**
 * Trait DataPermissionTrait
 * @package api\traits
 *
 * 只适用于ActiveQuery
 */
trait DataPermissionTrait
{
    /**
     * @param AdminUser $admin
     * @return \yii\db\ActiveQuery
     */
    public function dataPermission(AdminUser $admin)
    {
        return $this->andWhere([
            Area::withDatabaseName('id') => ArrayHelper::getColumn($admin->getCurrentAreas(), 'id')
        ]);
    }
}