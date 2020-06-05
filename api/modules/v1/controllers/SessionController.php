<?php
namespace api\modules\v1\controllers;

use api\components\Controller;
use api\forms\SessionForm;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\ArrayHelper;


/**
 * Class SessionController
 * @package api\modules\v1\controllers
 */
class SessionController extends Controller
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'class' => HttpBearerAuth::class,
                'except' => ['create']
            ]
        ]);
    }

    /**
     * @return SessionForm|array
     * @throws \yii\base\Exception
     */
    public function actionCreate()
    {
        $form = new SessionForm();
        $form->setScenario('login');
        $form->load(\Yii::$app->request->post(), '');
        if (!$form->validate()) {
            return $form;
        }

        $admin = $form->getUser();
        return [
            'token' => $admin->generateToken(),
            'admin' => $admin->toArray([], ['roles']),
            'env' => YII_ENV
        ];
    }

    /**
     * 设置当前用户角色
     *
     * @param int $id
     * @return SessionForm|array
     * @throws \yii\base\UserException
     */
    public function actionUpate(int $id)
    {
        $admin = \Yii::$app->user->identity;

        $form = new SessionForm();
        $form->setScenario('role');
        $form->load(\Yii::$app->request->post(), '');
        if (!$form->validate()) {
            return $form;
        }
        return $form->update($admin);
    }

    /**
     * Logs out the current user.
     *
     * @param int $id
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actionDelete(int $id)
    {
        $admin = \Yii::$app->user->identity;

        $admin->destroyToken();

        return [];
    }

    /**
     * 获取登录用户当前信息
     *
     * @param int $id
     * @return array
     */
    public function actionView(int $id)
    {
        return \Yii::$app->user->identity;
    }
}
