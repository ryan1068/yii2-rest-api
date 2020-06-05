<?php
/**
 * User: Ryan
 * Date: 2020/4/13
 * Time: 13:59
 */

namespace api\forms;

use api\models\ActiveRecord;
use api\models\OrgAdmin;
use api\models\OrgArea;
use api\models\query\AreaQuery;
use api\models\query\OrgQuery;
use api\resources\Area;
use api\resources\Org;
use common\validators\ArrayValidator;
use yii\base\Model;
use yii\base\UserException;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * Class OrgForm
 * @package api\forms
 */
class OrgForm extends Model
{
    public $pid;
    public $areaIds;
    public $name;
    public $orgId;
    public $pagination;
    public $mode;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['name', 'pid'], 'required', 'on' => [ActiveRecord::SCENARIO_UPDATE, ActiveRecord::SCENARIO_CREATE]],

            [['name', 'mode'], 'string'],
            [['name', 'mode'], 'trim'],
            [['mode'], 'in', 'range' => ['add', 'update']],

            [['pid', 'orgId', 'pagination'], 'integer'],
            [['pid'], 'default' , 'value' => 0],
            [['orgId'], 'required', 'on' => [ActiveRecord::SCENARIO_UPDATE, ActiveRecord::SCENARIO_VIEW]],

            [['areaIds'], 'default' , 'value' => []],
            [['areaIds'], ArrayValidator::class],
        ];
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[ActiveRecord::SCENARIO_CREATE] = ['pid', 'areaIds', 'name'];
        $scenarios[ActiveRecord::SCENARIO_UPDATE] = ['orgId', 'pid', 'areaIds', 'name'];
        $scenarios[ActiveRecord::SCENARIO_VIEW] = ['orgId', 'mode'];
        return $scenarios;
    }

    /**
     * 创建组织
     * @return array
     * @throws UserException
     */
    public function create()
    {
        $tran = Org::getDb()->beginTransaction();
        try {
            $org = new Org();
            $org->name = $this->name;
            $org->pid = $this->pid;
            if (!$org->save()) {
                throw new UserException('创建组织失败');
            }

            foreach ($this->areaIds as $areaId) {
                $orgArea = new OrgArea();
                $orgArea->org_id = $org->id;
                $orgArea->area_id = $areaId;
                if (!$orgArea->save()) {
                    throw new UserException('创建组织4S店失败');
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
     * 更新组织
     * @return array
     * @throws UserException
     */
    public function update()
    {
        $tran = Org::getDb()->beginTransaction();
        try {

            $org = $this->getOrg();
            $org->name = $this->name;
            $org->pid = $this->pid;
            if (!$org->save()) {
                throw new UserException('更新组织失败');
            }

            // 添加4S店
            $orgAreas = $org->getOrgAreas()->active()->all();
            $orgAreaIds = ArrayHelper::getColumn($orgAreas, 'area_id');

            $addIds = array_diff($this->areaIds, $orgAreaIds);
            foreach ($addIds as $areaId) {
                $orgArea = new OrgArea();
                $orgArea->org_id = $org->id;
                $orgArea->area_id = $areaId;
                if (!$orgArea->save()) {
                    throw new UserException('新增组织4S店失败');
                }
            }

            // 删除4S店
            $delIds = array_diff($orgAreaIds, $this->areaIds);
            $delOrgAreas = array_filter($orgAreas, function ($item) use ($delIds) {
                return ArrayHelper::isIn($item['area_id'], $delIds);
            });

            foreach ($delOrgAreas as $orgArea) {
                /* @var $orgArea OrgAdmin */
                $orgArea->is_del = OrgArea::IS_DEL_YES;
                if (!$orgArea->save()) {
                    throw new UserException('更新组织4S店失败');
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
     * 删除组织
     * @return array
     * @throws UserException
     */
    public function delete()
    {
        $tran = Org::getDb()->beginTransaction();
        try {

            $org = $this->getOrg();
            $org->is_del = Org::IS_DEL_YES;
            if (!$org->save()) {
                throw new UserException('删除组织失败');
            }

            //解绑4S店
            OrgArea::updateAll(['is_del' => Org::IS_DEL_YES], ['org_id' => $this->orgId]);

            //解绑管理人员
            OrgAdmin::updateAll(['is_del' => Org::IS_DEL_YES], ['org_id' => $this->orgId]);

            $tran->commit();
        } catch (\Exception $e) {
            $tran->rollBack();
            throw new UserException($e->getMessage());
        }

        return [];
    }

    /**
     * @return \api\models\Org|array|null
     * @throws UserException
     */
    public function getOrg()
    {
        $org = Org::find()->id($this->orgId)->active()->one();
        if (!$org) {
            throw new UserException('未找到组织');
        }
        return $org;
    }

    /**
     * 查询组织待选择的门店，下级组织只能获取上级组织的店，且下级组织平级之间的店互斥
     * @return ActiveDataProvider
     */
    public function getUnselectedAreas()
    {
        $method = 'getQueryBy' . ucfirst($this->mode);
        /* @var $query AreaQuery */
        $query = $this->$method();

        $dataProvider = new ActiveDataProvider([
            'query' => $query->defaultOrderBy(),
        ]);

        if (isset($this->pagination) && empty($this->pagination)) {
            $dataProvider->pagination = false;
        }

        if (!$this->validate()) {
            $query->where('0=1');
        }

        return $dataProvider;
    }

    /**
     * 获取新增组织时待选择的店
     * @return \api\models\query\AreaQuery
     * @throws UserException
     */
    public function getQueryByAdd()
    {
        if (isset($this->orgId) && $this->orgId > 0) {
            $org = $this->getOrg();

            // 下级所有组织已选择的店
            $subQuery = Area::find()
                ->joinWith(['orgs' => function (OrgQuery $query) use ($org) {
                    $query->pid($org->id)->active();
                }])
                ->active();

            // 获取本组织选的店和下一级组织已选店的差集
            $query = Area::find()
                ->joinWith(['orgs' => function (OrgQuery $query) use ($org) {
                    $query->id($org->id)->active();
                }])
                ->leftJoin(['b' => $subQuery], Area::withDatabaseName('id') .'= b.id')
                ->active()
                ->andWhere(['b.id' => null]);
        } else {
            // 如果是一级组织，可选所有的门店
            $query = Area::find()->active();
        }

        return $query;
    }

    /**
     * 获取更新组织时待选择的店
     * @return \api\models\query\AreaQuery
     * @throws UserException
     */
    public function getQueryByUpdate()
    {
        $org = $this->getOrg();

        if ($org->pid == 0) {
            // 获取一级组织待选门店

            // 一级组织已选门店
            $subQuery = Area::find()
                ->joinWith(['orgs' => function (OrgQuery $query) {
                    $query->id($this->orgId)->active();
                }])
                ->active();

            // 获取所有门店和一级组织已选门店的差集
            $query = Area::find()
                ->leftJoin(['b' => $subQuery], Area::withDatabaseName('id') .'= b.id')
                ->active()
                ->andWhere(['b.id' => null]);

        } else {
            // 获取子组织待选门店

            // 子组织的平级组织已选择的店
            $subQuery = Area::find()
                ->joinWith(['orgs' => function (OrgQuery $query) use ($org) {
                    $query->pid($org->pid)
                        ->active();
                }])
                ->active();

            // 获取上级组织选的店和下一级组织已选店的差集
            $query = Area::find()
                ->joinWith(['orgs' => function (OrgQuery $query) use ($org) {
                    $query->id($org->pid)->active();
                }])
                ->leftJoin(['b' => $subQuery], Area::withDatabaseName('id') .'= b.id')
                ->active()
                ->andWhere(['b.id' => null]);
        }

        return $query;
    }

}