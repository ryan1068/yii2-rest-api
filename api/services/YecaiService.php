<?php
/**
 * User: Ryan
 * Date: 2020/6/3
 * Time: 15:22
 */

namespace api\services;

/**
 * Class YecaiService
 * @package api\services
 */
class YecaiService extends BaseService
{
    /**
     * @var string yecai client
     */
    public $client;

    /**
     * @var string yecai secret
     */
    public $secret;

    /**
     * @var string auth scope
     */
    public $scope;

    /**
     * @var string auth grantType
     */
    public $grantType;

    const API_AUTH = '/auth/oauth/token';

    /**
     * 生成业财请求token
     * @return string
     */
    public function generateYecaiToken()
    {
        return base64_encode($this->client .':'. $this->secret);
    }

    /**
     * 业财用户认证
     * @param $username
     * @param $password
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function auth($username, $password)
    {
        $token = $this->generateYecaiToken();

        return $this->request(
            self::API_AUTH,
            [
                'scope' => $this->scope,
                'grant_type' => $this->grantType,
                'username' => $username,
                'password' => $password,
            ],
            'GET',
            [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Authorization' => "Basic {$token}",
            ]
        );
    }
}