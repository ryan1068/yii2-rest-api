<?php

namespace api\components\log;

use yii\helpers\ArrayHelper;
use yii\log\Logger;
use yii\log\Target;

/**
 * Class ProfileTarget
 * @package common\components\log
 */
class ProfileTarget extends Target
{
    /**
     * @var float
     */
    public $millisecond = 150.00;

    public $tableName;
    public $prefix;

    /**
     * @inheritdoc
     */
    public function export()
    {
        $timings = $this->calculateTimings($this->messages);
        ArrayHelper::multisort($timings, 'duration', SORT_DESC);

        $data = [];
        foreach ($timings as $seq => $profileTiming) {
            $traces = [];
            foreach ($profileTiming['trace'] as $trace) {
                $traces[] = "in {$trace['file']}:{$trace['line']}";
            }

            $duration = $profileTiming['duration'] * 1000;
            if ($duration >= $this->millisecond) {
                $data[] = [
                    'prefix' => $this->prefix,
                    'date' => date('Y-m-d H:i:s', $profileTiming['timestamp']),
                    'duration' => $duration,
                    'category' => $profileTiming['category'],
                    'info' => $profileTiming['info'],
                    'traces' => implode("\n    ", $traces),
                ];
            }
        }

        if ($data) {
            $log = \Yii::$app->mongodb->getCollection($this->tableName);
            $log->batchInsert($data);
        }
    }

    /**
     * @param $messages
     * @return array
     */
    protected function calculateTimings($messages)
    {
        $timings = [];
        $stack = [];

        foreach ($messages as $i => $log) {
            list($token, $level, $category, $timestamp, $traces) = $log;
            $memory = isset($log[5]) ? $log[5] : 0;
            $log[6] = $i;
            if ($level == Logger::LEVEL_PROFILE_BEGIN) {
                $stack[] = $log;
            } elseif ($level == Logger::LEVEL_PROFILE_END) {
                if (($last = array_pop($stack)) !== null && $last[0] === $token) {
                    $timings[$last[6]] = [
                        'info' => $last[0],
                        'category' => $last[2],
                        'timestamp' => $last[3],
                        'trace' => $last[4],
                        'level' => count($stack),
                        'duration' => $timestamp - $last[3],
                        'memory' => $memory,
                        'memoryDiff' => $memory - (isset($last[5]) ? $last[5] : 0),
                    ];
                }
            }
        }

        ksort($timings);

        return array_values($timings);
    }
}