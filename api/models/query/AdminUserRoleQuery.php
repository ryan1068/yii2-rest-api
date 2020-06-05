<?php

namespace api\models\query;
use api\models\AdminUserRole;

/**
 * This is the ActiveQuery class for [[\api\models\AdminUserRole]].
 *
 * @see \api\models\AdminUserRole
 */
class AdminUserRoleQuery extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return \api\models\AdminUserRole[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \api\models\AdminUserRole|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @return AdminUserRoleQuery
     */
    public function enable()
    {
        return $this->andWhere([AdminUserRole::withDatabaseName('status') => AdminUserRole::STATUS_ENABLE]);
    }

    /**
     * @return AdminUserRoleQuery
     */
    public function disable()
    {
        return $this->andWhere([AdminUserRole::withDatabaseName('status') => AdminUserRole::STATUS_DISABLE]);
    }

    /**
     * @param bool $bool
     * @return AdminUserRoleQuery
     */
    public function active($bool = true)
    {
        return $this->andWhere([AdminUserRole::withDatabaseName('is_del') => (bool)$bool ? 0 : 1]);
    }

    /**
     * @param int $roleId
     * @return AdminUserRoleQuery
     */
    public function roleId(int $roleId)
    {
        return $this->andWhere([AdminUserRole::withDatabaseName('role_id') => $roleId]);
    }

    /**
     * @param int $adminId
     * @return AdminUserRoleQuery
     */
    public function adminId(int $adminId)
    {
        return $this->andWhere([AdminUserRole::withDatabaseName('admin_id') => $adminId]);
    }

    /**
     * @param $adminId
     * @return AdminUserRoleQuery
     */
    public function groupByAdminId()
    {
        return $this->groupBy([AdminUserRole::withDatabaseName('admin_id')]);
    }
}
