<?php

namespace api\modules\v1;

/**
 * Class Module
 * @package api\modules\v1
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'api\modules\v1\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }
}
