<?php

namespace api\traits;

/**
 * Trait ArrayTrait
 * @package api\traits
 */
trait ArrayTrait
{
    /**
     * 去除多维数组数字键名，返回的资源格式方便前端使用
     * @param array $array
     * @return array
     */
    public static function clearKey(array $array)
    {
        $newArray = [];
        foreach ($array as $key => $arr) {
            if (is_array($arr)) {
                if (is_int($key)) {
                    $newArray[] = self::clearKey($arr);
                } else {
                    $newArray[$key] = self::clearKey($arr);
                }
            } else {
                if (is_int($key)) {
                    $newArray[] = $arr;
                } else {
                    $newArray[$key] = $arr;
                }
            }
        }
        return $newArray;
    }

    /**
     * 移除数组中指定元素并返回移除后的数组
     * @param array $except
     * @return array
     */
    public function removeAttributes(array $except = [])
    {
        $newArray = [];
        foreach ($this->attributes() as $key => $val) {
            if (in_array($val, $except)) {
                continue;
            }
            $newArray[$key] = $val;
        }
        return $newArray;
    }
    
    /**
     * 指定格式字符串转为数组，只适用于Model
     * @param $attribute
     */
    public function convertArray($attribute)
    {
        if (is_array($this->$attribute)) {
            return;
        }

        $this->$attribute = explode(',', $this->$attribute);
        $this->$attribute = array_map(function ($item) {
            return trim($item);
        }, $this->$attribute);
        $this->$attribute = array_unique($this->$attribute);
    }

    /**
     * 获取模型安全属性，只适用于Model
     * @param array $except
     * @return mixed
     */
    public function getSafeAttributes(array $except = [])
    {
        return $this->getAttributes($this->safeAttributes(), $except);
    }
}