<?php

namespace api\models\query;

use api\models\AreaAdminUser;

/**
 * This is the ActiveQuery class for [[\api\models\AreaAdminUser]].
 *
 * @see \api\models\AreaAdminUser
 */
class AreaAdminUserQuery extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return \api\models\AreaAdminUser[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \api\models\AreaAdminUser|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param bool $bool
     * @return AreaAdminUserQuery
     */
    public function active(bool $bool = true)
    {
        return $this->andWhere([AreaAdminUser::withDatabaseName('is_del') => $bool ? 0 : 1]);
    }
}
