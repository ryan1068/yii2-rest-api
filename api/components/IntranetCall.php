<?php
namespace api\components;

use yii\helpers\IpHelper;

/**
 * Class IntranetCall
 * @package api\components
 */
class IntranetCall extends \yii\filters\auth\HttpBearerAuth
{
    /**
     * @var string 客户端ip
     */
    public $clientIp;

    /**
     * @var array 内网ip网段
     */
    const IPS = [
        '10.*', '172.*', '192.168.*', '127.0.0.1'
    ];

    /**
     * 内网访问跳过身份鉴权
     * @param \yii\web\User $user
     * @param \yii\web\Request $request
     * @param \yii\web\Response $response
     * @return bool|null|\yii\web\IdentityInterface
     * @throws \yii\base\NotSupportedException
     * @throws \yii\web\UnauthorizedHttpException
     */
    public function authenticate($user, $request, $response)
    {
        //带token时验证token
        if ($auth = parent::authenticate($user, $request, $response)) {
            return $auth;
        }

        //不带token时验证是否是内网调用
        if ($this->matchIP()) {
            return true;
        }

        return null;
    }

    /**
     * 获取客户端ip
     * @return mixed|null|string
     */
    protected function getClientIp()
    {
        return $this->clientIp ?: \Yii::$app->request->userIP;
    }

    /**
     * 匹配内网ip
     * @return bool
     * @throws \yii\base\NotSupportedException
     */
    protected function matchIP()
    {
        $userIp = $this->getClientIp();
        \Yii::info($userIp, __METHOD__);

        foreach (self::IPS as $rule) {
            if ($rule === '*' ||
                $rule === $userIp ||
                (
                    $userIp !== null &&
                    ($pos = strpos($rule, '*')) !== false &&
                    strncmp($userIp, $rule, $pos) === 0
                ) ||
                (
                    ($pos = strpos($rule, '/')) !== false &&
                    IpHelper::inRange($userIp, $rule) === true
                )
            ) {
                return true;
            }
        }

        return false;
    }
}