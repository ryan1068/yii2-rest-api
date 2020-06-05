<?php
/**
 * User: Ryan
 * Date: 2020/4/14
 * Time: 17:09
 */

namespace api\services;

use api\models\RolePermission;
use api\resources\Column;
use api\resources\Org;
use api\resources\Role;
use api\traits\ArrayTrait;
use yii\base\Component;

/**
 * Class AccountService
 * @package api\services
 */
class AccountService extends Component
{
    /**
     * 获取组织架构
     * @param int $pid
     * @return \api\models\Org[]|array
     */
    public static function getOrgStruct(int $pid = 0)
    {
        $struct = [];
        $orgs = Org::find()
            ->pid($pid)
            ->active()
            ->defaultOrderBy()
            ->all();
        foreach ($orgs as $org) {
            $org = $org->toArray();
            $struct[$org['id']] = $org;
            $child = self::getOrgStruct($org['id']);
            if ($child) {
                $struct[$org['id']]['child'] = $child;
            }
        }
        return ArrayTrait::clearKey($struct);
    }

    /**
     * 获取角色权限树
     * @param int $pid
     * @param int $roleId
     * @return array
     */
    public static function getPermissionTree(int $pid = 0, int $roleId = 0)
    {
        $permissionTree = [];
        $columns = Column::find()
            ->active()
            ->pid($pid)
            ->defaultOrderBy()
            ->all();
        foreach ($columns as $column) {
            $column = $column->toArray();
            $permissionTree[$column['id']] = $column;
            if ($roleId) {
                $role = Role::findOne($roleId);
                if ($role->isAdministrator()) {
                    $permissionTree[$column['id']]['checked'] = true;
                    if (strpos($column['permission'], 'system:work') !== false) {
                        $permissionTree[$column['id']]['checked'] = false;
                    }
                } else {
                    $checked = RolePermission::find()
                        ->active()
                        ->andWhere([
                            'role_id' => $roleId,
                            'column_id' => $column['id']
                        ])
                        ->exists();
                    $permissionTree[$column['id']]['checked'] = $checked;
                }
            }

            $child = self::getPermissionTree($column['id'], $roleId);
            if ($child) {
                $permissionTree[$column['id']]['child'] = $child;
            }
        }
        return ArrayTrait::clearKey($permissionTree);
    }
}