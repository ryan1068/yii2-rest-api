<?php

namespace api\models\query;
use api\models\RolePermission;

/**
 * This is the ActiveQuery class for [[\api\models\RolePermission]].
 *
 * @see \api\models\RolePermission
 */
class RolePermissionQuery extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return \api\models\RolePermission[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \api\models\RolePermission|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param bool $bool
     * @return RolePermissionQuery
     */
    public function active(bool $bool = true)
    {
        return $this->andWhere([RolePermission::withDatabaseName('is_del') => (bool)$bool ? 0 : 1]);
    }
}
