<?php

namespace common\behaviors;


use yii\base\Behavior;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class ResponseFormatBehavior extends Behavior
{
    public function events()
    {
        return array(
            Response::EVENT_BEFORE_SEND => 'formatResponse'
        );
    }

    public function formatResponse($event)
    {
        $request = \Yii::$app->request;
        /* @var $response Response */
        $response = $event->sender;
        if ($response->data !== null && is_array($response->data)) {
            if ($response->isSuccessful) {
                $response->data = [
                    'code' => 200,
                    'msg' => 'ok',
                    'data' => $response->data,
                ];

                $response->statusCode = 200;
            } else if ($response->isNotFound) {
                $response->data = [
                    'code' => 404,
                    'msg' => \Yii::t('exception', ArrayHelper::getValue($response->data, 'message')),
                    'data' => false,
                ];

                $response->statusCode = 200;
            } else if ($response->statusCode == 422) {
                //Data Validate error
                $response->data = [
                    'code' => 422,
                    'msg' => ArrayHelper::getValue($response->data, '0.message'),
                    'data' => $response->data,
                ];

                $response->statusCode = 200;
            } else if ($response->statusCode == 401) {
                //授权失败
                $response->data = [
                    'code' => 401,
                    'msg' => \Yii::t('exception', ArrayHelper::getValue($response->data, 'message')),
                    'data' => false,
                ];

                $response->statusCode = 401;
            } else {
                //系统错误
                $response->data = [
                    'code' => ArrayHelper::getValue($response->data, 'code') ?: $response->statusCode,
                    'msg' => \Yii::t('exception', ArrayHelper::getValue($response->data, 'message')),
                    'data' => false,
                ];

                $response->statusCode = 200;
            }

            if ($request->method == 'OPTIONS') {
                $response->statusCode = 200;
            }

            \Yii::info(ArrayHelper::toArray($response->data), 'response.data');
        }

        if($response->data == null) {
            $response->data = [
                'code' => 200,
                'msg' => 'ok',
                'data' => $response->data,
            ];
            $response->statusCode = 200;
        }
    }
}