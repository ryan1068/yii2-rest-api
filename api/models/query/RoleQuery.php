<?php

namespace api\models\query;
use api\models\Role;

/**
 * This is the ActiveQuery class for [[\api\models\Role]].
 *
 * @see \api\models\Role
 */
class RoleQuery extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return \api\models\Role[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \api\models\Role|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param bool $bool
     * @return RoleQuery
     */
    public function active(bool $bool = true)
    {
        return $this->andWhere([Role::withDatabaseName('is_del') => $bool ? 0 : 1]);
    }

    /**
     * @param int $id
     * @return RoleQuery
     */
    public function id(int $id)
    {
        return $this->andWhere([Role::withDatabaseName('id') => $id]);
    }

    /**
     * @param string|array $roleKey
     * @return RoleQuery
     */
    public function roleKey($roleKey)
    {
        return $this->andWhere([Role::withDatabaseName('role_key') => $roleKey]);
    }

    /**
     * @return RoleQuery
     */
    public function defaultOrderBy()
    {
        return $this->orderBy([
            Role::withDatabaseName('sort') => SORT_ASC,
            Role::withDatabaseName('created_at') => SORT_DESC
        ]);
    }
}
