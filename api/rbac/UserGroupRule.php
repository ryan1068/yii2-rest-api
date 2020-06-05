<?php
/**
 * User: Ryan
 * Date: 2020/4/26
 * Time: 14:43
 */

namespace api\rbac;

use api\resources\Role;
use yii\helpers\ArrayHelper;
use yii\rbac\Rule;
use Yii;

/**
 * Class UserGroupRule
 * @package api\rbac
 */
class UserGroupRule extends Rule
{
    public $name = 'userGroup';

    /**
     * @param int|string $user
     * @param \yii\rbac\Item $item
     * @param array $params
     * @return bool
     */
    public function execute($user, $item, $params)
    {
        if (!Yii::$app->user->isGuest) {
            $admin = Yii::$app->user->identity;
            $role = $admin->getCurrentRole();
            $roleKey = ArrayHelper::getValue($role, 'role_key');

            if ($item->name === Role::ROLE_ADMINISTRATOR) {
                Yii::info([$role->toArray()], __METHOD__);
                return $roleKey == Role::ROLE_ADMINISTRATOR;
            } elseif ($item->name === 'dcc_inviter') {
                return $roleKey == Role::ROLE_DCC_INVITER || $roleKey == Role::ROLE_ADMINISTRATOR;
            } elseif ($item->name === 'dcc_supervisor') {
                return $roleKey == Role::ROLE_DCC_SUPERVISOR || $roleKey == Role::ROLE_ADMINISTRATOR;
            }
        }
        return false;
    }
}