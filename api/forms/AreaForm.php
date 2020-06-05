<?php
/**
 * User: Ryan
 * Date: 2020/4/13
 * Time: 13:59
 */

namespace api\forms;

use api\models\ActiveRecord;
use api\models\AreaAdminUser;
use api\models\AreaManufacture;
use api\models\query\AreaQuery;
use api\resources\Area;
use api\resources\Org;
use common\validators\ArrayValidator;
use yii\base\DynamicModel;
use yii\base\Model;
use yii\base\UserException;
use yii\helpers\ArrayHelper;

/**
 * Class AreaForm
 * @package api\forms
 */
class AreaForm extends Model
{
    /**
     * @var int 车商通4S店id
     */
    public $areaId;

    public $fullName;
    public $shortName;
    public $address;

    /**
     * @var string 高科应用id
     */
    public $caloaiId;

    /**
     * @var string 高科应用密钥
     */
    public $caloaiKey;

    /**
     * @var array 店铺车型数据
     */
    public $manufactures;

    /**
     * @var int 店铺管理员名称
     */
    public $adminName;

    /**
     * @var array 车型数据模型
     */
    private $_manufactureModels;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['areaId', 'fullName', 'shortName', 'address', 'manufactures', 'adminName'], 'required', 'on' => [ActiveRecord::SCENARIO_CREATE]],

            [['fullName', 'shortName', 'address', 'adminName', 'caloaiId', 'caloaiKey'], 'string'],
            [['fullName', 'shortName', 'address', 'adminName', 'caloaiId', 'caloaiKey'], 'trim'],

            [['manufactures'], ArrayValidator::class],
            [['manufactures'], 'convertManufactures'],

            [['areaId'], 'required', 'on' => [ActiveRecord::SCENARIO_UPDATE, ActiveRecord::SCENARIO_VIEW]],
            [['areaId'], 'integer'],
            [['areaId'], 'unique', 'targetAttribute' => 'id', 'targetClass' => Area::class, 'filter' => function (AreaQuery $query) {
                return $query->active();
            }, 'on' => ActiveRecord::SCENARIO_CREATE],

            [['areaId'], 'exist', 'targetAttribute' => 'id', 'targetClass' => Area::class, 'filter' => function (AreaQuery $query) {
                return $query->active();
            }, 'except' => ActiveRecord::SCENARIO_CREATE],
        ];
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[ActiveRecord::SCENARIO_CREATE] = ['areaId', 'fullName', 'shortName', 'address', 'manufactures', 'adminName', 'caloaiId', 'caloaiKey'];
        $scenarios[ActiveRecord::SCENARIO_UPDATE] = ['areaId', 'fullName', 'shortName', 'address', 'manufactures', 'caloaiId', 'caloaiKey'];
        $scenarios[ActiveRecord::SCENARIO_VIEW] = ['areaId'];
        $scenarios[ActiveRecord::SCENARIO_DELETE] = ['areaId'];
        return $scenarios;
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'areaId' => '4S店id',
            'fullName' => '4S店全称',
            'shortName' => '4S店简称',
            'address' => '地址',
            'manufactures' => '车型',
            'adminName' => '管理员名称',
            'caloaiId' => '高科应用id',
            'caloaiKey' => '高科应用密钥',
        ];
    }

    /**
     * 店铺车型数据转换成模型
     * @param $attribute
     * @param $params
     * @throws UserException
     */
    public function convertManufactures($attribute, $params)
    {
        foreach ($this->manufactures as $manufacture) {
            $this->_manufactureModels[] = $this->createManufactureModel($manufacture);
        }
    }

    /**
     * 创建店铺
     * @return array|DynamicModel
     * @throws UserException
     */
    public function create()
    {
        $tran = Area::getDb()->beginTransaction();
        try {
            //创建4S店
            $area = new Area();
            $area->id = $this->areaId;
            $area->cst_area_id = $this->areaId;
            $area->full_name = $this->fullName;
            $area->short_name = $this->shortName;
            $area->address = $this->address;
            $area->caloai_id = $this->caloaiId;
            $area->caloai_key = $this->caloaiKey;

            if (!$area->save()) {
                throw new UserException('创建4S店失败');
            }

            // 创建车型数据
            foreach ($this->_manufactureModels as $manufacture) {
                $areaManufacture = new AreaManufacture();
                $areaManufacture->area_id = $area->id;
                $areaManufacture->cat_id = $manufacture['cat_id'];
                $areaManufacture->brand_id = $manufacture['brand_id'];
                $areaManufacture->brand = $manufacture['brand'];
                $areaManufacture->manufacture = $manufacture['manufacture'];

                if (!$areaManufacture->save()) {
                    throw new UserException('创建车型失败');
                }
            }

            // 创建管理员
            $admin = new AreaAdminUser();
            $admin->area_id = $area->id;
            $admin->name = $this->adminName;
            if (!$admin->save()) {
                throw new UserException('创建管理员失败');
            }

            $tran->commit();
        } catch (\Exception $e) {
            $tran->rollBack();
            throw new UserException($e->getMessage());
        }

        return [];
    }

    /**
     * 更新店铺
     * @return array|DynamicModel
     * @throws UserException
     */
    public function update()
    {
        $tran = Area::getDb()->beginTransaction();
        try {
            // 更新店铺
            $area = $this->getArea();
            $area->full_name = $this->fullName;
            $area->short_name = $this->shortName;
            $area->address = $this->address;
            $area->caloai_id = $this->caloaiId;
            $area->caloai_key = $this->caloaiKey;

            if (!$area->save()) {
                throw new UserException('更新4S店失败');
            }

            // 更新车型数据
            $areaManufactures = $area->getAllManufactures();
            $areaManufactureIds = ArrayHelper::getColumn($areaManufactures, 'cat_id');
            $postManufactureIds = ArrayHelper::getColumn($this->_manufactureModels, 'cat_id');

            // add manufacture
            $addManufactureIds = array_diff($postManufactureIds, $areaManufactureIds);
            $addManufactures = array_filter($this->_manufactureModels, function ($item) use ($addManufactureIds) {
                return ArrayHelper::isIn($item['cat_id'], $addManufactureIds);
            });

            foreach ($addManufactures as $manufacture) {
                $areaManufacture = new AreaManufacture();
                $areaManufacture->area_id = $area->id;
                $areaManufacture->cat_id = $manufacture['cat_id'];
                $areaManufacture->brand_id = $manufacture['brand_id'];
                $areaManufacture->brand = $manufacture['brand'];
                $areaManufacture->manufacture = $manufacture['manufacture'];

                if (!$areaManufacture->save()) {
                    \Yii::info($areaManufacture->errors, __METHOD__);
                    throw new UserException('更新4S店车型失败');
                }
            }

            // delele manufacture
            $delManufactureIds = array_diff($areaManufactureIds, $postManufactureIds);
            $delManufactures = array_filter($areaManufactures, function ($item) use ($delManufactureIds) {
                return ArrayHelper::isIn($item['cat_id'], $delManufactureIds);
            });

            foreach ($delManufactures as $manufacture) {
                /* @var $manufacture AreaManufacture */
                $manufacture->is_del = ActiveRecord::IS_DEL_YES;

                if (!$manufacture->save()) {
                    \Yii::info($manufacture->errors, __METHOD__);
                    throw new UserException('更新4S店车型失败');
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
     * 创建车型数据模型
     * @param $manufacture
     * @return DynamicModel
     * @throws UserException
     */
    public function createManufactureModel($manufacture)
    {
        $manufactureModel = new DynamicModel(['cat_id', 'brand_id', 'brand', 'manufacture']);
        $manufactureModel->addRule(['cat_id', 'brand_id', 'brand', 'manufacture'], 'required')
            ->addRule(['brand', 'manufacture'], 'string')
            ->addRule(['cat_id', 'brand_id'], 'integer');
        $manufactureModel->load($manufacture, '');
        if (!$manufactureModel->validate()) {
            throw new UserException(current($manufactureModel->firstErrors));
        }

        return $manufactureModel;
    }

    /**
     * 删除店铺
     * @return array
     * @throws UserException
     */
    public function delete()
    {
        $tran = Org::getDb()->beginTransaction();
        try {

            $area = $this->getArea();
            $area->is_del = ActiveRecord::IS_DEL_YES;
            if (!$area->save()) {
                throw new UserException('删除店铺失败');
            }

            $tran->commit();
        } catch (\Exception $e) {
            $tran->rollBack();
            throw new UserException($e->getMessage());
        }

        return [];
    }

    /**
     * @return Area|array|null
     */
    public function getArea()
    {
        $area = Area::find()
            ->active()
            ->id($this->areaId)
            ->one();

        return $area;
    }
}