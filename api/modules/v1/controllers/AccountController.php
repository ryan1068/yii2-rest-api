<?php
namespace api\modules\v1\controllers;

use api\components\Controller;
use api\components\IntranetCall;
use api\forms\AccountForm;
use api\searches\AdminSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * Class AccountController
 * @package api\modules\v1\controllers
 */
class AccountController extends Controller
{
    public $modelClass = 'api\resources\AdminUser';

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
                'except' => ['index', 'view'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['system:setting:account', 'administrator'],
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
     * @return AdminSearch|\yii\data\ActiveDataProvider
     */
    public function actionIndex()
    {
        $search = new AdminSearch();
        $search->load(\Yii::$app->request->get(), '');
        if (!$search->validate()) {
            return $search;
        }
        return $search->search();
    }

    /**
     * @return AccountForm|array
     * @throws \yii\base\UserException
     */
    public function actionCreate()
    {
        $form = new AccountForm();
        $form->setScenario($this->createScenario);
        $form->load(\Yii::$app->request->post(), '');
        if (!$form->validate()) {
            return $form;
        }
        return $form->create();
    }

    /**
     * @param $id
     * @return AccountForm|array
     * @throws \yii\base\UserException
     * @throws \Throwable
     */
    public function actionUpdate(int $id)
    {
        $form = new AccountForm();
        $form->setScenario($this->updateScenario);
        $form->load(\Yii::$app->request->post(), '');
        $form->accountId = $id;
        if (!$form->validate()) {
            return $form;
        }
        return $form->update();
    }

    /**
     * @param $id
     * @return AccountForm|array
     * @throws \yii\base\UserException
     */
    public function actionDelete(string $id)
    {
        $form = new AccountForm();
        $form->setScenario($this->deleteScenario);
        $form->load(\Yii::$app->request->post(), '');
        $form->accountId = $id;
        if (!$form->validate()) {
            return $form;
        }
        return $form->delete();
    }

    /**
     * @param int $id
     * @return AccountForm|array
     */
    public function actionView(int $id)
    {
        $form = new AccountForm();
        $form->setScenario($this->viewScenario);
        $form->load(\Yii::$app->request->post(), '');
        $form->accountId = $id;
        if (!$form->validate()) {
            return $form;
        }
        $admin = $form->getAdminUser();
        return compact('admin');
    }

    /**
     * @param int $id
     * @return AccountForm|array
     * @throws \yii\base\UserException
     */
    public function actionEnable(int $id)
    {
        $form = new AccountForm();
        $form->setScenario($this->viewScenario);
        $form->accountId = $id;
        if (!$form->validate()) {
            return $form;
        }
        return $form->enable();
    }

    /**
     * @param int $id
     * @return AccountForm|array
     * @throws \yii\base\UserException
     */
    public function actionDisable(int $id)
    {
        $form = new AccountForm();
        $form->setScenario($this->viewScenario);
        $form->accountId = $id;
        if (!$form->validate()) {
            return $form;
        }
        return $form->disable();
    }
}
