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
    public $uri;

    /**
     * @param $api
     */
    public function setUri($api)
    {
        $this->uri = $this->host . $api;
    }

    /**
     * @return mixed
     */
    public function getUri()
    {
        return $this->uri;
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
        $this->setUri($api);
        $response = HttpTrait::request(
            $this->uri,
            $data,
            $method,
            $headers
        );

        \Yii::info($response, 'httpCall');
        return $response;
    }
}