<?php

/**
 * 用于增强IDE代码自动完成。
 * 使用方式：右键(vendor/yiisoft/yii2/Yii.php) -> "Mark as Plain Text"
 */
class Yii extends \yii\BaseYii
{
    /**
     * @var BaseApplication|WebApplication|ConsoleApplication the application instance
     */
    public static $app;
}

/**
 * Class BaseApplication
 * Used for properties that are identical for both WebApplication and ConsoleApplication
 *
 * @property yii\db\Connection $db
 * @property yii\db\Connection $cardb
 * @property yii\mongodb\Connection $mongodb
 * @property \yii\queue\Queue $queue
 * @property yii\httpclient\Client $http
 * @property yii\redis\Mutex $mutex
 * @property yii\redis\Session $session
 * @property yii\queue\Queue $esQueue
 * @property api\services\YecaiService $yecai
 * @property api\services\CallCenterService $callCenter
 *
 */
abstract class BaseApplication extends yii\base\Application
{
}

/**
 * Class WebApplication
 * Include only Web application related components here
 *
 * @property User $user
 */
class WebApplication extends yii\web\Application
{
}

/**
 * Class ConsoleApplication
 * Include only Console application related components here
 */
class ConsoleApplication extends yii\console\Application
{
}

/**
 * @property \api\models\resources\AdminUser|yii\web\IdentityInterface|null $identity
 */
class User extends \yii\web\User
{

}