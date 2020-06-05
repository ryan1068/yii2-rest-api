<?php

namespace api\models\query;
use api\models\OrgAdmin;

/**
 * This is the ActiveQuery class for [[\api\models\OrgAdmin]].
 *
 * @see \api\models\OrgAdmin
 */
class OrgAdminQuery extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return \api\models\OrgAdmin[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \api\models\OrgAdmin|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function active($bool = true)
    {
        return $this->andWhere([OrgAdmin::withDatabaseName('is_del') => (bool)$bool ? 0 : 1]);
    }

    public function org($orgId)
    {
        return $this->andWhere([OrgAdmin::withDatabaseName('org_id') => (int)$orgId]);
    }

    public function admin($adminId)
    {
        return $this->andWhere([OrgAdmin::withDatabaseName('admin_id') => (int)$adminId]);
    }

    public function role($roleId)
    {
        return $this->andWhere([OrgAdmin::withDatabaseName('role_id') => (int)$roleId]);
    }

}
