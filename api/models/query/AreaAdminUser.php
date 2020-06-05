<?php

namespace api\models\query;

/**
 * This is the ActiveQuery class for [[\api\models\AreaAdminUser]].
 *
 * @see \api\models\AreaAdminUser
 */
class AreaAdminUser extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

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
}
