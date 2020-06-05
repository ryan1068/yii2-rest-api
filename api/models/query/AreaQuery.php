<?php

namespace api\models\query;

use api\models\Area;

/**
 * This is the ActiveQuery class for [[\api\models\WArea]].
 *
 * @see \api\models\Area
 */
class AreaQuery extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return \api\models\Area[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \api\models\Area|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param bool $bool
     * @return AreaQuery
     */
    public function active(bool $bool = true)
    {
        return $this->andWhere([Area::withDatabaseName('is_del') => $bool ? 0 : 1]);
    }

    /**
     * @param int $id
     * @return AreaQuery
     */
    public function id(int $id)
    {
        return $this->andWhere([Area::withDatabaseName('id') => $id]);
    }

    /**
     * @param int $id
     * @return AreaQuery
     */
    public function cstId(int $id)
    {
        return $this->andWhere([Area::withDatabaseName('cst_area_id') => $id]);
    }

    /**
     * @return AreaQuery
     */
    public function defaultOrderBy()
    {
        return $this->orderBy(['created_at' => SORT_DESC]);
    }
}
