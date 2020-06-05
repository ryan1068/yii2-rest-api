<?php

namespace api\models\query;

use api\models\AreaManufacture;

/**
 * This is the ActiveQuery class for [[\api\models\AreaManufacture]].
 *
 * @see \api\models\AreaManufacture
 */
class AreaManufactureQuery extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return \api\models\AreaManufacture[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \api\models\AreaManufacture|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param bool $bool
     * @return AreaManufactureQuery
     */
    public function active(bool $bool = true)
    {
        return $this->andWhere([AreaManufacture::withDatabaseName('is_del') => $bool ? 0 : 1]);
    }

}
