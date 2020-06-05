<?php

namespace api\traits;

/**
 * Class DatabaseNameTrait
 * @package common\traits
 *
 * 只适用于ActiveRecord
 */
trait DatabaseNameTrait
{
    /**
     * 返回字段完整名称，包含数据库名称
     * @param $attribute
     * @return string
     */
    public static function withDatabaseName($attribute)
    {
        $tableName = self::tableName();

        return "{$tableName}.{$attribute}";
    }
}