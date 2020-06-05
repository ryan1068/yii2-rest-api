<?php
/**
 * User: Ryan
 * Date: 2020/4/13
 * Time: 17:35
 */

namespace api\resources;


/**
 * Class AreaManufacture
 * @package api\resources
 */
class AreaManufacture extends \api\models\AreaManufacture
{
    /**
     * @return array
     */
    public function fields()
    {
        return [
            'brandId' => 'brand_id',
            'name' => 'brand',
            'manufactureId' => 'cat_id',
            'manufactureName' => 'manufacture',
            'image' => function () {
                return '';
            }
        ];
    }

}