<?php

namespace api\components\log;


use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use yii\log\Target;

class DingTarget extends Target
{
    public $url;

    /**
     * Exports log [[messages]] to a specific destination.
     * Child classes must implement this method.
     */
    public function export()
    {
        $client = new Client();
        $client->transport = CurlTransport::class;

        $requests = [];
        foreach ($this->messages as $message) {
            list($text, $level, $category, $timestamp) = $message;
            if (!is_string($text)) {
                // exceptions may not be serializable if in the call stack somewhere is a Closure
                if ($text instanceof \Throwable || $text instanceof \Exception) {
                    $text = VarDumper::export((string)$text);
                } else {
                    $text = VarDumper::export($text);
                }
            }

            $date = date('Y-m-d H:i:s', $timestamp);
            $id = ArrayHelper::getValue(\Yii::$app, 'id', '');
            $env = ArrayHelper::getValue($this->getEnvName(), YII_ENV);
            $category = VarDumper::export($category);
            $path = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . ($_SERVER['argv'] ? " | ".var_export((array)$_SERVER['argv'],1) : "");
            $params = var_export($_REQUEST, 1);
            $msg = json_encode([
                'msgtype' => 'markdown',
                'markdown' => [
                    'title' => "[{$env}][{$id}][{$this->prefix}]",
                    'text' => "
#### [{$env}][{$id}][{$this->prefix}][{$date}][{$category}][$path][$params]
{$text}",
                ],
            ]);

            $request = $client->post($this->url);
            $request->setHeaders(['Content-Type' => 'application/json']);
            $request->setContent($msg);

            $requests[] = $request;
        }

        $client->batchSend($requests);
    }

    /**
     * @return array
     */
    protected function getEnvName()
    {
        return [
            'dev' => '开发环境',
            'test' => '测试环境',
            'pre' => '预发布环境',
            'prod' => '正式环境',
            'sit-test' => 'sit-test',
            'dev-test' => 'dev-test',
            'ab' => 'ab'
        ];
    }
}