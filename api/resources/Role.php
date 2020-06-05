<?php
/**
 * User: Ryan
 * Date: 2020/4/13
 * Time: 17:35
 */

namespace api\resources;


use api\services\AccountService;

/**
 * Class Role
 * @package api\resources
 */
class Role extends \api\models\Role
{
    /**
     * @return array
     */
    public function fields()
    {
        return [
            'id',
            'pid',
            'name',
            'type',
            'desc',
            'role_key',
            'sort',
        ];
    }

    /**
     * @return array
     */
    public function extraFields()
    {
        return [
            'permissions' => function () {
                return AccountService::getPermissionTree(0, $this->id);
            },
            'userCount' => function () {
                return $this->getAdminUsers()
                    ->active()
                    ->count();
            }
        ];
    }

    /**
     * @return \api\models\AdminUser[]|array
     */
    public function getAllAdminUsers()
    {
        return $this->getAdminUsers()
            ->active()
            ->all();
    }
}