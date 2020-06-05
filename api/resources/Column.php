<?php
/**
 * User: Ryan
 * Date: 2020/4/13
 * Time: 17:35
 */

namespace api\resources;


class Column extends \api\models\Column
{
    public function fields()
    {
        return [
            'id',
            'pid',
            'name',
            'url',
            'remark',
            'icon',
            'visible',
            'permission'
        ];
    }
}