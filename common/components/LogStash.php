<?php

namespace common\components;


use yii\base\BaseObject;
use yii\queue\Job;
use yii\queue\Queue;

class LogStash extends BaseObject implements Job
{
    /**
     * 搜集的数据
     * @var array
     */
    public $data;

    /**
     * 表名
     * @var string
     */
    public $collection;

    /**
     * @param Queue $queue
     * @return mixed|void
     * @throws \yii\mongodb\Exception
     */
    public function execute($queue)
    {
        $log = \Yii::$app->mongodb->getCollection($this->collection);
        $log->insert($this->data);
    }
}