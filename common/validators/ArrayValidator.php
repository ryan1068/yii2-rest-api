<?php

namespace common\validators;

use yii\validators\Validator;

class ArrayValidator extends Validator
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->message === null) {
            $this->message = \Yii::t('yii', '{attribute}必须为数组');
        }
    }

    /**
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        $valid = true;
        if (!is_array($value)) {
            $valid = false;
        }

        return $valid ? null : [$this->message, []];
    }
}