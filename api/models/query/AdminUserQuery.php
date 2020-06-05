<?php

namespace api\models\query;
use api\models\AdminUser;

/**
 * This is the ActiveQuery class for [[\api\models\AdminUser]].
 *
 * @see \api\models\AdminUser
 */
class AdminUserQuery extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return \api\models\AdminUser[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \api\models\AdminUser|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param bool $bool
     * @return AdminUserQuery
     */
    public function active(bool $bool = true)
    {
        return $this->andWhere([AdminUser::withDatabaseName('is_del') => $bool ? 0 : 1]);
    }

    /**
     * @param int $id
     * @return AdminUserQuery
     */
    public function id(int $id)
    {
        return $this->andWhere([AdminUser::withDatabaseName('id') => $id]);
    }

    /**
     * @return AdminUserQuery
     */
    public function enable()
    {
        return $this->andWhere([AdminUser::withDatabaseName('status') => AdminUser::STATUS_ENABLE]);
    }

    /**
     * @return AdminUserQuery
     */
    public function disable()
    {
        return $this->andWhere([AdminUser::withDatabaseName('status') => AdminUser::STATUS_DISABLE]);
    }
}
