<?php
/**
 * User: Ryan
 * Date: 2020/6/3
 * Time: 15:22
 */

namespace api\services;

use api\traits\HttpTrait;
use yii\base\Component;

/**
 * Class BaseService
 * @package api\services
 */
abstract class BaseService extends Component
{
    use HttpTrait;

    /**
     * @var string
     */
    public $host;

    /**
     * @var string
     */
    public $apiUrl;

    /**
     * @param $api
     */
    public function setApiUrl($api)
    {
        $this->apiUrl = $this->host . $api;
    }

    /**
     * @return mixed
     */
    public function getApiUrl()
    {
        return $this->apiUrl;
    }

    /**
     * @param $api
     * @param array $data
     * @param string $method
     * @param array $headers
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function request($api, $data = [], $method = 'POST', $headers = ['Content-Type' => 'application/x-www-form-urlencoded'])
    {
        $this->setApiUrl($api);
        $response = HttpTrait::request(
            $this->apiUrl,
            $data,
            $method,
            $headers
        );

        \Yii::info($response, 'httpCall');
        return $response;
    }
}