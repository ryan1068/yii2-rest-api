<?php

namespace api\resources;


use api\models\query\AdminUserRoleQuery;
use api\models\query\OrgAdminQuery;
use api\traits\AccountTrait;
use yii\filters\RateLimitInterface;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\IdentityInterface;
use Yii;

/**
 * Class AdminUser
 * @package api\resources
 *
 * @property int currentRoleId
 * @property Role currentRole
 * @property Org[] currentOrgs
 * @property Area[] currentAreas
 * @property Role[] userRoles
 * @property array permissionTree
 */
class AdminUser extends \api\models\AdminUser implements IdentityInterface,RateLimitInterface
{
    /**
     * @param int|string $id
     * @return AdminUser|null|IdentityInterface
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * @param mixed $token
     * @param null $type
     * @return AdminUser|null|IdentityInterface
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $session = \Yii::$app->session->readSession($token);
        if (!$session) {
            return null;
        }
        \Yii::info($session);
        return AdminUser::findOne(Json::decode($session)['id']);
    }

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getCurrentRoleId()
    {
        return ArrayHelper::getValue($this, 'currentRole.id', 0);
    }

    /**
     * @return bool
     */
    public function isAdministrator()
    {
        return $this->currentRole ? $this->currentRole->isAdministrator() : false;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return false;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return \Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param $password
     * @throws \yii\base\Exception
     */
    public function setPassword($password)
    {
        $this->password = $this->generatePasswordHash($password);
    }

    /**
     * @param $password
     * @return string
     * @throws \yii\base\Exception
     */
    public function generatePasswordHash($password)
    {
        return \Yii::$app->security->generatePasswordHash($password);
    }
    
    /**
     * @return string
     */
    public function getCacheKey()
    {
        return "call-center-account-{$this->id}";
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return \Yii::$app->session->readSession($this->getCacheKey());
    }

    /**
     * 生成token
     * @param bool $forceRefresh
     * @return string
     * @throws \yii\base\Exception
     */
    public function generateToken(bool $forceRefresh = false)
    {
        $isAdministrator = $this->isAdministrator();

        if ($forceRefresh && !$isAdministrator) {
            $this->destroyToken();
        }

        if ($token = $this->getToken()) {
            return $token;
        }
        
        $token = \Yii::$app->security->generateRandomString();
        \Yii::$app->session->writeSession($this->getCacheKey(), $token);
        \Yii::$app->session->writeSession($token, Json::encode($this->toArray()));

        return $token;
    }

    /**
     * refresh token
     * @return string|bool
     * @throws \yii\base\Exception
     */
    public function refreshToken()
    {
        if ($this->destroyToken()) {
            return $this->generateToken();
        }
        return false;
    }

    /**
     * 清除用户登录信息
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function destroyToken()
    {
        \Yii::$app->session->destroySession($this->getToken());
        \Yii::$app->session->destroySession($this->getCacheKey());

        //设置坐席退出状态
        Yii::$app->callCenter->setState($this->id, -1);

        return true;
    }

    private $_currentRole;

    /**
     * 获取用户当前角色
     * @return Role|array|null|\yii\db\ActiveRecord
     */
    public function getCurrentRole()
    {
        if (!isset($this->_currentRole)) {
            $this->_currentRole = $this->getRoles()
                ->joinWith(['adminUserRoles' => function (AdminUserRoleQuery $query) {
                    return $query->active()
                        ->adminId($this->id)
                        ->enable();
                }])
                ->active()
                ->one();
        }
        return $this->_currentRole;
    }

    private $_currentOrgs;

    /**
     * 获取用户当前角色管理的组织
     * @return Org[]|array|\yii\db\ActiveRecord[]
     */
    public function getCurrentOrgs()
    {
        if (!isset($this->_currentOrgs)) {
            $this->_currentOrgs = $this->getOrgs()
                ->joinWith(['orgAdmins' => function (OrgAdminQuery $query) {
                    return $query->admin($this->id)
                        ->role($this->currentRoleId)
                        ->active();
                }])
                ->active()
                ->all();
        }
        return $this->_currentOrgs;
    }

    private $_currentAreas;

    /**
     * 获取用户当前角色管理的4S店
     * @return \api\models\Area[]|array
     */
    public function getCurrentAreas()
    {
        if (!isset($this->_currentAreas)) {
            $this->_currentAreas = Area::find()
                ->joinWith('orgs')
                ->joinWith(['orgAdmins' => function (OrgAdminQuery $query) {
                    return $query->admin($this->id)
                        ->role($this->currentRoleId)
                        ->active();
                }])
                ->active()
                ->all();
        }
        return $this->_currentAreas;
    }

    /**
     * 获取用户所有角色
     * @return \api\models\Role[]|array
     */
    public function getAllRoles()
    {
        return $this->getRoles()
            ->joinWith(['adminUserRoles' => function (AdminUserRoleQuery $query) {
                return $query->active()
                    ->adminId($this->id);
            }])
            ->active()
            ->defaultOrderBy()
            ->all();
    }

    /**
     * 获取用户所有角色和对应的管理组织
     * @return \api\models\Role[]|array
     */
    public function getUserRolesAndOrgs()
    {
        $scopes = [];
        foreach ($this->getAllRoles() as $key => $role) {
            $scopes[$key] = $role->toArray();
            $scopes[$key]['orgs'] = $this->getOrgs()
                ->joinWith(['orgAdmins' => function (OrgAdminQuery $query) use ($role) {
                    return $query->active()
                        ->role($role->id);
                }])
                ->active()
                ->all();
        }
        return $scopes;
    }

    /**
     * 获取用户当前角色权限树
     * @return array
     */
    public function getPermissionTree()
    {
        return AccountTrait::getPermissionTree(0, $this->currentRoleId);
    }

    /**
     * @var int 请求次数限制
     */
    public $rateLimit = 500;

    /**
     * @var int 请求单位时间
     */
    public $timeLimit = 10;

    /**
     * 返回允许的请求的最大数目及时间，例如，[100, 600] 表示在 600 秒内最多 100 次的 API 调用。
     * @param \yii\web\Request $request
     * @param \yii\base\Action $action
     * @return array
     */
    public function getRateLimit($request, $action)
    {
        return [$this->rateLimit, $this->timeLimit];
    }

    /**
     * 返回剩余的允许的请求和最后一次速率限制检查时 相应的 UNIX 时间戳数。
     * @param \yii\web\Request $request
     * @param \yii\base\Action $action
     * @return array|mixed
     */
    public function loadAllowance($request, $action)
    {
        $key = $this->generateKeyIdentity($this->id, Yii::$app->requestedRoute);
        $data = Yii::$app->redis->get($key);

        if (empty($data)) {
            $data = [$this->rateLimit, time()];
        } else {
            $data = Json::decode($data);
        }

        return $data;
    }

    /**
     * 保存剩余的允许请求数和当前的 UNIX 时间戳。
     * @param \yii\web\Request $request
     * @param \yii\base\Action $action
     * @param int $allowance
     * @param int $timestamp
     */
    public function saveAllowance($request, $action, $allowance, $timestamp)
    {
        $key = $this->generateKeyIdentity($this->id, Yii::$app->requestedRoute);
        $data = [$allowance, $timestamp];
        Yii::$app->redis->setex($key, $this->timeLimit, Json::encode($data, true));
    }

    /**
     * 生成速率限制 唯一标识 key
     * @param $userId
     * @param $route
     * @return string
     */
    public function generateKeyIdentity($userId, $route)
    {
        Yii::info([$userId, $route]);
        $keyPrefix = substr(md5(Yii::$app->id), 0, 5);
        return $keyPrefix . md5($userId.$route);
    }

    /**
     * @return array
     */
    public function fields()
    {
        return [
            'id',
            'account',
            'nickname',
            'tel',
            'status',
            'type',
            'current_role_id',
            'createdDate' => function () {
                return date('Y-m-d H:i', $this->created_at);
            },
        ];
    }

    /**
     * @var int 组织id
     */
    public static $orgId;

    /**
     * @return array
     */
    public function extraFields()
    {
        return [
            'currentRole' => function () {
                return $this->currentRole;
            },
            'permissions' => function () {
                return $this->permissionTree;
            },
            'orgs' => function () {
                return $this->currentOrgs;
            },
            'areas' => function () {
                return $this->currentAreas;
            },
            'roles' => function () {
                return $this->getUserRolesAndOrgs();
            },
            'orgRole' => function () {
                if (self::$orgId) {
                    $orgAdmin = $this->getOrgAdmins()
                        ->andWhere(['org_id' => self::$orgId])
                        ->one();

                    return Role::findOne($orgAdmin->role_id);
                }
                return [];
            },
        ];
    }
}