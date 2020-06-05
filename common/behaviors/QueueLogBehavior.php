<?php

namespace common\behaviors;

use common\components\LogStash;
use Yii;
use yii\base\Behavior;
use yii\helpers\VarDumper;
use yii\queue\ExecEvent;
use yii\queue\Job;
use yii\queue\JobEvent;
use yii\queue\PushEvent;
use yii\queue\Queue;

class QueueLogBehavior extends Behavior
{
    /**
     * @var Queue
     */
    public $owner;
    /**
     * @var bool
     */
    public $autoFlush = true;

    private $_start;

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            Queue::EVENT_AFTER_PUSH => 'afterPush',
            Queue::EVENT_BEFORE_EXEC => 'beforeExec',
            Queue::EVENT_AFTER_EXEC => 'afterExec',
            Queue::EVENT_AFTER_ERROR => 'afterExecError',
        ];
    }

    /**
     * @param PushEvent $event
     * @throws \yii\mongodb\Exception
     */
    public function afterPush(PushEvent $event)
    {
        if ($this->except($event->job)) {
            return;
        }

        $log = Yii::$app->mongodb->getCollection('queue_log');
        $log->insert([
            'queue_id' => (string)$event->id,
            'name' => $event->job instanceof Job ? get_class($event->job) : 'mixed data',
            'timeout' => $event->delay,
            'create_time' => time(),
            'create_date' => date('Y-m-d H:i:s'),
            'start_time' => 0,
            'start_date' => '',
            'end_time' => 0,
            'end_date' => '',
            'spending' => 0,
            'status' => 0,
            'message' => '',
            'data' => get_object_vars($event->job),
        ]);
    }

    /**
     * @param JobEvent $event
     * @throws \yii\mongodb\Exception
     */
    public function beforeExec(JobEvent $event)
    {
        if ($this->except($event->job)) {
            return;
        }

        $this->_start = microtime(true);
        $log = Yii::$app->mongodb->getCollection('queue_log');
        $now = time();
        $log->update(['queue_id' => (string)$event->id], [
            'start_time' => $now,
            'start_date' => date('Y-m-d H:i:s', $now),
        ]);
    }

    /**
     * @param JobEvent $event
     * @throws \yii\mongodb\Exception
     */
    public function afterExec(JobEvent $event)
    {
        if ($this->except($event->job)) {
            return;
        }

        $log = Yii::$app->mongodb->getCollection('queue_log');
        $now = time();
        $log->update(['queue_id' => (string)$event->id], [
            'status' => 1,
            'end_time' => $now,
            'end_date' => date('Y-m-d H:i:s', $now),
            'spending' => microtime(true) - $this->_start,
            'message' => 'success',
        ]);
    }

    /**
     * @param ExecEvent $event
     * @throws \yii\mongodb\Exception
     */
    public function afterExecError(ExecEvent $event)
    {
        if ($this->except($event->job)) {
            return;
        }

        $log = Yii::$app->mongodb->getCollection('queue_log');
        $now = time();
        $log->update(['queue_id' => (string)$event->id], [
            'status' => 2,
            'end_time' => $now,
            'end_date' => date('Y-m-d H:i:s', $now),
            'spending' => (microtime(true) - $this->_start) * 1000,
            'message' => VarDumper::export($event->error),
        ]);
    }

    /**
     * 排除记录的任务
     * @param $job
     * @return bool
     */
    protected function except($job)
    {
        if ($job instanceof LogStash) {
            return true;
        }

        return false;
    }
}