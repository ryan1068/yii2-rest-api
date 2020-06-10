<?php
namespace api\modules\v1\controllers;

use api\components\Controller;
use api\components\IntranetCall;
use api\forms\OrgForm;
use api\traits\AccountTrait;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * Class OrgController
 * @package api\modules\v1\controllers
 */
class OrgController extends Controller
{
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
                        'roles' => ['system:setting:org', 'administrator'],
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
     * 首页
     * @return array
     */
    public function actionIndex()
    {
        $orgStruct = AccountTrait::getOrgStruct();
        return compact('orgStruct');
    }

    /**
     * @return OrgForm|array
     * @throws \yii\base\UserException
     */
    public function actionCreate()
    {
        $form = new OrgForm();
        $form->setScenario($this->createScenario);
        $form->load(\Yii::$app->request->post(), '');
        if (!$form->validate()) {
            return $form;
        }
        return $form->create();
    }

    /**
     * @param int $id
     * @return OrgForm|array
     * @throws \yii\base\UserException
     */
    public function actionUpdate(int $id)
    {
        $form = new OrgForm();
        $form->setScenario($this->updateScenario);
        $form->load(\Yii::$app->request->post(), '');
        $form->orgId = $id;
        if (!$form->validate()) {
            return $form;
        }
        return $form->update();
    }

    /**
     * @param int $id
     * @return OrgForm|array
     * @throws \yii\base\UserException
     */
    public function actionDelete(int $id)
    {
        $form = new OrgForm();
        $form->setScenario($this->viewScenario);
        $form->load(\Yii::$app->request->post(), '');
        $form->orgId = $id;
        if (!$form->validate()) {
            return $form;
        }
        return $form->delete();
    }

    /**
     * @param int $id
     * @return OrgForm|array
     * @throws \yii\base\UserException
     */
    public function actionView(int $id)
    {
        $form = new OrgForm();
        $form->setScenario($this->viewScenario);
        $form->load(\Yii::$app->request->get(), '');
        $form->orgId = $id;
        if (!$form->validate()) {
            return $form;
        }
        $org = $form->getOrg();
        return compact('org');
    }

    /**
     * 获取组织待选择的门店
     * @param int $id
     * @return OrgForm|array
     * @throws \yii\base\UserException
     */
    public function actionUnselectedAreas(int $id)
    {
        $form = new OrgForm();
        $form->setScenario($this->viewScenario);
        $form->load(\Yii::$app->request->get(), '');
        $form->orgId = $id;
        if (!$form->validate()) {
            return $form;
        }
        $areas = $form->getUnselectedAreas();
        return compact('areas');
    }

}
