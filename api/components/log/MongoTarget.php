<?php

namespace api\components\log;


use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\helpers\VarDumper;
use yii\log\Target;
use yii\mongodb\Connection;

class MongoTarget extends Target
{

    /**
     * @var Connection|array|string the DB connection object or the application component ID of the DB connection.
     * After the DbTarget object is created, if you want to change this property, you should only assign it
     * with a DB connection object.
     * Starting from version 2.0.2, this can also be a configuration array for creating the object.
     */
    public $mongo = 'mongodb';
    /**
     * @var string name of the DB table to store cache content. Defaults to "log".
     */
    public $logTable = 'log';


    /**
     * Initializes the DbTarget component.
     * This method will initialize the [[db]] property to make sure it refers to a valid DB connection.
     * @throws InvalidConfigException if [[db]] is invalid.
     */
    public function init()
    {
        parent::init();
        $this->mongo = Instance::ensure($this->mongo, Connection::className());
    }

    /**
     * Stores log messages to DB.
     */
    public function export()
    {
        $collection = $this->mongo->getCollection($this->logTable);

        foreach ($this->messages as $message) {
            list($text, $level, $category, $timestamp, $traces, $memory) = $message;
            $prefix = $this->getMessagePrefix($message);
            if (!is_string($text)) {
                // exceptions may not be serializable if in the call stack somewhere is a Closure
                if ($text instanceof \Throwable || $text instanceof \Exception) {
                    $text = (string)$text;
                } else {
                    $text = VarDumper::export($text);
                }
            }

            $collection->insert([
                'level' => $level,
                'category' => $category,
                'prefix' => $prefix,
                'message' => $text,
                'memory' => sprintf('%.3f MB', $memory / 1048576),
                'timestamp' => $timestamp,
                'datetime' => \Yii::$app->formatter->asDatetime(time()),
                'userId' => \Yii::$app->has('user')
                    ? ArrayHelper::getValue(\Yii::$app->user, 'id', 0)
                    : 0
            ]);
        }
    }
}