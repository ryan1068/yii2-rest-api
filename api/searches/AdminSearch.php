<?php

namespace api\searches;


use api\models\AdminUserRole;
use api\models\Org;
use api\models\query\AdminUserQuery;
use api\models\query\RoleQuery;
use api\resources\AdminUser;
use api\resources\Role;
use api\traits\ArrayTrait;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * Class AdminSearch
 * @package api\searches
 */
class AdminSearch extends Model
{
    use ArrayTrait;

    public $orgId;
    public $keyword;
    public $status;
    public $roleId;
    public $roleKey;
    public $type;
    public $pagination;
    public $adminId;
    public $all;

    /**
     * @var string|array 过滤角色标识
     */
    public $filterRoleKey;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['keyword', 'adminId'], 'string'],
            [['roleKey', 'filterRoleKey'], 'safe'],
            [['roleKey', 'filterRoleKey', 'adminId'], 'convertArray'],
            [['orgId', 'roleId', 'status', 'type', 'all'], 'integer'],
            [['pagination'], 'boolean'],
        ];
    }

    public function afterValidate()
    {
        parent::afterValidate();
        AdminUser::$orgId = $this->orgId;
    }

    /**
     * @return ActiveDataProvider
     */
    public function search()
    {
        $query = AdminUser::find();

        if ($this->all != 1) {
            $query->active();
        }

        $query->joinWith('roles')
            ->andWhere(['<>', Role::withDatabaseName('role_key'), Role::ROLE_ADMINISTRATOR]);

        // 按组织过滤
        if ($this->orgId) {
            $query->joinWith('orgs')
                ->andWhere([Org::withDatabaseName('id') => $this->orgId]);
        }

        // 按角色过滤
        $this->filterByRole($query);

        $query->andFilterWhere([
            AdminUser::withDatabaseName('status') => $this->status,
            AdminUser::withDatabaseName('type') => $this->type,
            AdminUser::withDatabaseName('id') => $this->adminId,
        ]);

        $query->andFilterWhere([
            'OR',
            ['LIKE', AdminUser::withDatabaseName('account'), $this->keyword],
            ['LIKE', AdminUser::withDatabaseName('nickname'), $this->keyword],
            ['LIKE', AdminUser::withDatabaseName('tel'), $this->keyword],
        ]);

        $query->groupBy([AdminUser::withDatabaseName('id')]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query->orderBy([AdminUser::withDatabaseName('created_at') => SORT_DESC])
        ]);

        if (isset($this->pagination) && empty($this->pagination)) {
            $dataProvider->pagination = false;
        }

        return $dataProvider;
    }

    /**
     * @param AdminUserQuery $query
     * @return AdminUserQuery
     */
    private function filterByRole(AdminUserQuery $query)
    {
        if ($this->roleId) {
            $query->andWhere([Role::withDatabaseName('id') => $this->roleId]);
        } elseif ($this->roleKey) {
            $query->andWhere([Role::withDatabaseName('role_key') => $this->roleKey]);
        } elseif ($this->filterRoleKey) {
            // 只要用户角色中包含filterRoleKey角色，则过滤该用户
            if (ArrayHelper::isIn('dcc', $this->filterRoleKey)) {
                $this->filterRoleKey = Role::getDccRoleKey();
            }

            $subQuery = AdminUserRole::find()
                ->select([AdminUserRole::withDatabaseName('admin_id')])
                ->joinWith(['role' => function (RoleQuery $query) {
                    $query->active()->roleKey($this->filterRoleKey);
                }])
                ->active()
                ->groupByAdminId();

            $query->andWhere(['NOT IN', AdminUser::withDatabaseName('id') , $subQuery]);
        }

        return $query;
    }
}