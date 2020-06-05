<?php
namespace api\modules\v1\controllers;

use api\components\Controller;
use api\components\IntranetCall;
use api\forms\OrgAdminForm;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * Class OrgAdminController
 * @package api\modules\v1\controllers
 */
class OrgAdminController extends Controller
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
     * @param int $orgId
     * @return OrgAdminForm|array
     * @throws \yii\base\UserException
     */
    public function actionCreate(int $orgId)
    {
        $form = new OrgAdminForm();
        $form->setScenario($this->createScenario);
        $form->load(\Yii::$app->request->post(), '');
        $form->orgId = $orgId;
        if (!$form->validate()) {
            return $form;
        }
        return $form->create();
    }

    /**
     * @param int $orgId
     * @param int $adminId
     * @return OrgAdminForm|array
     * @throws \yii\base\UserException
     */
    public function actionUpdate(int $orgId, int $adminId)
    {
        $form = new OrgAdminForm();
        $form->setScenario($this->updateScenario);
        $form->load(\Yii::$app->request->post(), '');
        $form->orgId = $orgId;
        $form->adminId = $adminId;
        if (!$form->validate()) {
            return $form;
        }
        return $form->update();
    }

    /**
     * @param int $orgId
     * @param string $adminId
     * @return OrgAdminForm|array
     * @throws \yii\base\UserException
     */
    public function actionDelete(int $orgId, string $adminId)
    {
        $form = new OrgAdminForm();
        $form->setScenario($this->deleteScenario);
        $form->load(\Yii::$app->request->post(), '');
        $form->orgId = $orgId;
        $form->adminId = $adminId;
        if (!$form->validate()) {
            return $form;
        }
        return $form->delete();
    }
}
