<?php

namespace api\models\query;
use api\models\Org;

/**
 * This is the ActiveQuery class for [[\api\models\Org]].
 *
 * @see \api\models\Org
 */
class OrgQuery extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return \api\models\Org[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \api\models\Org|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param bool $bool
     * @return OrgQuery
     */
    public function active(bool $bool = true)
    {
        return $this->andWhere([Org::withDatabaseName('is_del') => $bool ? 0 : 1]);
    }

    /**
     * @param int $id
     * @return OrgQuery
     */
    public function id(int $id)
    {
        return $this->andWhere([Org::withDatabaseName('id') => $id]);
    }

    /**
     * @param int $pid
     * @return OrgQuery
     */
    public function pid(int $pid)
    {
        return $this->andWhere([Org::withDatabaseName('pid') => $pid]);
    }

    /**
     * @return OrgQuery
     */
    public function defaultOrderBy()
    {
        return $this->orderBy([
            Org::withDatabaseName('sort') => SORT_ASC,
            Org::withDatabaseName('created_at') => SORT_DESC
        ]);
    }
}
