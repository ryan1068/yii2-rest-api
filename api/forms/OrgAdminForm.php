<?php
/**
 * User: Ryan
 * Date: 2020/4/13
 * Time: 13:59
 */

namespace api\forms;

use api\models\ActiveRecord;
use api\models\OrgAdmin;
use api\models\query\OrgAdminQuery;
use api\traits\ArrayTrait;
use yii\base\Model;
use yii\base\UserException;

/**
 * Class OrgAdminForm
 * @package api\forms
 */
class OrgAdminForm extends Model
{
    use ArrayTrait;

    public $orgId;
    public $adminId;
    public $roleId;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['orgId', 'adminId', 'roleId'], 'required', 'on' => [ActiveRecord::SCENARIO_UPDATE, ActiveRecord::SCENARIO_CREATE]],
            [['orgId', 'roleId'], 'integer'],
            [['orgId', 'adminId'], 'required', 'on' => ActiveRecord::SCENARIO_DELETE],

            [['adminId'], 'string', 'on' => ActiveRecord::SCENARIO_DELETE],
            [['adminId'], 'trim', 'on' => ActiveRecord::SCENARIO_DELETE],
            [['adminId'], 'convertArray', 'on' => ActiveRecord::SCENARIO_DELETE],

            [['adminId'], 'unique', 'targetAttribute' => 'admin_id', 'targetClass' => OrgAdmin::class,
                'filter' => function (OrgAdminQuery $query) {
                    return $query->org($this->orgId)
                        ->admin($this->adminId)
                        ->active();
                },
                'on' => ActiveRecord::SCENARIO_CREATE,
                'message' => '该管理人员已添加，请勿重复添加'
            ],
        ];
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[ActiveRecord::SCENARIO_CREATE] = ['orgId', 'adminId', 'roleId'];
        $scenarios[ActiveRecord::SCENARIO_UPDATE] = ['orgId', 'adminId', 'roleId'];
        $scenarios[ActiveRecord::SCENARIO_DELETE] = ['orgId', 'adminId'];
        return $scenarios;
    }

    /**
     * 创建管理人员
     * @return array
     * @throws UserException
     */
    public function create()
    {
        $orgAdmin = new OrgAdmin();
        $orgAdmin->org_id = $this->orgId;
        $orgAdmin->admin_id = $this->adminId;
        $orgAdmin->role_id = $this->roleId;
        if (!$orgAdmin->save()) {
            throw new UserException('新增管理人员失败');
        }

        return [];
    }

    /**
     * 更新管理人员
     * @return array
     * @throws UserException
     */
    public function update()
    {
        $orgAdmin = $this->getOrgAdmin();
        $orgAdmin->role_id = $this->roleId;
        if (!$orgAdmin->save()) {
            throw new UserException('更新管理人员失败');
        }

        return [];
    }

    /**
     * 删除管理人员
     * @return array
     * @throws UserException
     */
    public function delete()
    {
        $tran = OrgAdmin::getDb()->beginTransaction();
        try {
            $orgAdmins = OrgAdmin::findAll([
                'org_id' => $this->orgId,
                'admin_id' => $this->adminId
            ]);
            foreach ($orgAdmins as $orgAdmin) {
                $orgAdmin->is_del = OrgAdmin::IS_DEL_YES;
                if (!$orgAdmin->save()) {
                    throw new UserException('删除管理人员失败');
                }
            }

            $tran->commit();
        } catch (\Exception $e) {
            $tran->rollBack();
            throw new UserException($e->getMessage());
        }
        return [];
    }

    /**
     * @return OrgAdmin|array|null
     * @throws UserException
     */
    public function getOrgAdmin()
    {
        $orgAdmin = OrgAdmin::find()
            ->org($this->orgId)
            ->admin($this->adminId)
            ->active()
            ->one();
        if (!$orgAdmin) {
            throw new UserException('未找到组织下管理人员');
        }
        return $orgAdmin;
    }
}