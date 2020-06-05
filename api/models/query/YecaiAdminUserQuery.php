<?php

namespace api\models\query;
use api\models\YecaiAdminUser;

/**
 * This is the ActiveQuery class for [[\api\models\YecaiAdminUser]].
 *
 * @see \api\models\YecaiAdminUser
 */
class YecaiAdminUserQuery extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return \api\models\YecaiAdminUser[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \api\models\YecaiAdminUser|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param bool $bool
     * @return YecaiAdminUserQuery
     */
    public function active(bool $bool = true)
    {
        return $this->andWhere([YecaiAdminUser::withDatabaseName('is_del') => $bool ? 0 : 1]);
    }

    /**
     * @return YecaiAdminUserQuery
     */
    public function enable()
    {
        return $this->andWhere([YecaiAdminUser::withDatabaseName('status') => YecaiAdminUser::STATUS_ENABLE]);
    }

    /**
     * @return YecaiAdminUserQuery
     */
    public function disable()
    {
        return $this->andWhere([YecaiAdminUser::withDatabaseName('status') => YecaiAdminUser::STATUS_DISABLE]);
    }
}
