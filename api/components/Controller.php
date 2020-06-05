<?php
namespace api\components;


use api\models\ActiveRecord;
use yii\filters\ContentNegotiator;
use yii\filters\RateLimiter;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * Class Controller
 * @package api\components
 */
class Controller extends \yii\rest\ActiveController
{
    /**
     * @var string the model class name.
     */
    public $modelClass = '';

    /**
     * @var string|array the configuration for creating the serializer that formats the response data.
     */
    public $serializer= 'api\components\Serializer';

    /**
     * 场景-创建
     */
    public $createScenario = ActiveRecord::SCENARIO_CREATE;

    /**
     * 场景-更新
     */
    public $updateScenario = ActiveRecord::SCENARIO_UPDATE;

    /**
     * 场景-查看
     */
    public $viewScenario = ActiveRecord::SCENARIO_VIEW;

    /**
     * 场景-删除
     */
    public $deleteScenario = ActiveRecord::SCENARIO_DELETE;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'verbFilter' => [
                'class' => VerbFilter::class,
                'actions' => $this->verbs(),
            ],
            'rateLimiter' => [
                'class' => RateLimiter::class,
                'enableRateLimitHeaders' => false
            ],
        ];
    }

    /**
     * @return array
     */
    public function actions()
    {
        $actions = parent::actions();

        unset(
            $actions['index'],
            $actions['view'],
            $actions['create'],
            $actions['update'],
            $actions['delete']
        );

        return $actions;
    }
}