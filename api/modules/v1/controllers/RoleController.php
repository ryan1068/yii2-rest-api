<?php
namespace api\modules\v1\controllers;

use api\components\Controller;
use api\components\IntranetCall;
use api\forms\RoleForm;
use api\searches\RoleSearch;
use api\traits\AccountTrait;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * Class RoleController
 * @package api\modules\v1\controllers
 */
class RoleController extends Controller
{
    public $modelClass = 'api\resources\Role';

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
                        'roles' => ['system:setting:role', 'administrator'],
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
     * @return RoleSearch|\yii\data\ActiveDataProvider
     */
    public function actionIndex()
    {
        $search = new RoleSearch();
        $search->load(\Yii::$app->request->get(), '');
        if (!$search->validate()) {
            return $search;
        }
        return $search->search();
    }

    /**
     * @return RoleForm|array
     * @throws \yii\base\UserException
     */
    public function actionCreate()
    {
        $form = new RoleForm();
        $form->setScenario($this->createScenario);
        $form->load(\Yii::$app->request->post(), '');
        if (!$form->validate()) {
            return $form;
        }
        return $form->create();
    }

    /**
     * @param int $id
     * @return RoleForm|array
     * @throws \yii\base\UserException
     */
    public function actionUpdate(int $id)
    {
        $form = new RoleForm();
        $form->setScenario($this->updateScenario);
        $form->load(\Yii::$app->request->post(), '');
        $form->roleId = $id;
        if (!$form->validate()) {
            return $form;
        }
        return $form->update();
    }

    /**
     * @param string $id
     * @return RoleForm|array
     * @throws \yii\base\UserException
     */
    public function actionDelete(string $id)
    {
        $form = new RoleForm();
        $form->setScenario($this->deleteScenario);
        $form->load(\Yii::$app->request->post(), '');
        $form->roleId = $id;
        if (!$form->validate()) {
            return $form;
        }
        return $form->delete();
    }

    /**
     * @param int $id
     * @return RoleForm|array
     * @throws \yii\base\UserException
     */
    public function actionView(int $id)
    {
        $form = new RoleForm();
        $form->setScenario($this->viewScenario);
        $form->load(\Yii::$app->request->get(), '');
        $form->roleId = $id;
        if (!$form->validate()) {
            return $form;
        }

        $role = $form->getRole();

        return compact('role');
    }

    /**
     * 获取权限树
     * @return array
     */
    public function actionPermissionTree()
    {
        $tree = AccountTrait::getPermissionTree();

        return compact('tree');
    }
}
