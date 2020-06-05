<?php

namespace api\models\query;
use api\models\OrgArea;

/**
 * This is the ActiveQuery class for [[\api\models\OrgArea]].
 *
 * @see \api\models\OrgArea
 */
class OrgAreaQuery extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return \api\models\OrgArea[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \api\models\OrgArea|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function active($bool = true)
    {
        return $this->andWhere([OrgArea::withDatabaseName('is_del') => (bool)$bool ? 0 : 1]);
    }
}
