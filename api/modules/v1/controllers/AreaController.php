<?php
namespace api\modules\v1\controllers;

use api\components\Controller;
use api\components\IntranetCall;
use api\forms\AreaForm;
use api\searches\AreaSearch;
use yii\base\UserException;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * Class AreaController
 * @package api\modules\v1\controllers
 */
class AreaController extends Controller
{
    public $modelClass = 'api\resources\Area';

    /**
     * @return array
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'class' => IntranetCall::class,
            ],
            'access' => [
                'class' => AccessControl::class,
                'only' => ['create', 'update', 'delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['system:setting:area', 'administrator'],
                    ],
                    [
                        'allow' => true,
                        'ips' => IntranetCall::IPS
                    ]
                ]
            ],
        ]);
    }

    /**
     * @return AreaSearch|\yii\data\ActiveDataProvider
     */
    public function actionIndex()
    {
        $search = new AreaSearch();
        $search->load(\Yii::$app->request->get(), '');
        if (!$search->validate()) {
            return $search;
        }
        return $search->search();
    }

    /**
     * @param int $id
     * @return AreaForm|\api\models\Area|array|null
     */
    public function actionView(int $id)
    {
        $form = new AreaForm();
        $form->setScenario($this->viewScenario);
        $form->load(\Yii::$app->request->get(), '');
        $form->areaId = $id;
        if (!$form->validate()) {
            return $form;
        }
        return $form->getArea();
    }

    /**
     * @return AreaForm|array
     * @throws \yii\base\UserException
     */
    public function actionCreate()
    {
        $form = new AreaForm();
        $form->setScenario($this->createScenario);
        $form->load(\Yii::$app->request->post(), '');
        if (!$form->validate()) {
            return $form;
        }
        return $form->create();
    }

    /**
     * @param $id
     * @return AreaForm|array
     * @throws \yii\base\UserException
     */
    public function actionUpdate(int $id)
    {
        $form = new AreaForm();
        $form->setScenario($this->updateScenario);
        $form->load(\Yii::$app->request->post(), '');
        $form->areaId = $id;
        if (!$form->validate()) {
            return $form;
        }
        return $form->update();
    }

    /**
     * @param $id
     * @return AreaForm|array
     * @throws \yii\base\UserException
     */
    public function actionDelete(string $id)
    {
        $form = new AreaForm();
        $form->setScenario($this->deleteScenario);
        $form->load(\Yii::$app->request->post(), '');
        $form->areaId = $id;
        if (!$form->validate()) {
            return $form;
        }
        return $form->delete();
    }
}
