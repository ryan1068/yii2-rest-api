<?php
/**
 * User: Ryan
 * Date: 2020/4/13
 * Time: 17:35
 */

namespace api\resources;


/**
 * Class Org
 * @package api\resources
 */
class Org extends \api\models\Org
{
    /**
     * @return array
     */
    public function fields()
    {
        return [
            'id',
            'pid',
            'name',
            'sort'
        ];
    }

    /**
     * @return array
     */
    public function extraFields()
    {
        return [
            'areas' => function () {
                return $this->getAreas()
                    ->active()
                    ->all();
            },
        ];
    }
}