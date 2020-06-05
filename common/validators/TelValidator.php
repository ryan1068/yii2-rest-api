<?php

namespace common\validators;

use yii\validators\Validator;

class TelValidator extends Validator
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->message = '您输入的手机号码格式错误';
    }

    /**
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        if (self::isMobile($value)) {
            return null;
        }

        return [$this->message, []];
    }

    /**
     * @param $string string
     * @return boolean
     */
    public static function isMobile($string)
    {
        if (empty($string)) {
            return false;
        }
        $exp = '#^1[1-9][\d]{9}$#';
        return preg_match($exp, $string);
    }
}