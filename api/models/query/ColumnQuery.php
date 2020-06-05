<?php

namespace api\models\query;

use api\models\Column;

/**
 * This is the ActiveQuery class for [[\api\models\Column]].
 *
 * @see \api\models\Column
 */
class ColumnQuery extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return \api\models\Column[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \api\models\Column|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param int $pid
     * @return ColumnQuery
     */
    public function pid(int $pid)
    {
        return $this->andWhere([Column::withDatabaseName('pid') => $pid]);
    }

    /**
     * @param bool $bool
     * @return ColumnQuery
     */
    public function active(bool $bool = true)
    {
        return $this->andWhere([Column::withDatabaseName('is_del') => (bool)$bool ? 0 : 1]);
    }

    /**
     * @return ColumnQuery
     */
    public function defaultOrderBy()
    {
        return $this->orderBy([Column::withDatabaseName('sort') => SORT_ASC]);
    }
}
