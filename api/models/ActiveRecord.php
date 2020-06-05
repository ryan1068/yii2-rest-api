<?php

namespace api\models;

use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base model.
 */
class ActiveRecord extends \yii\db\ActiveRecord
{
    /**
     * 已删除
     */
    const IS_DEL_YES = 1;

    /**
     * 未删除
     */
    const IS_DEL_NO = 0;

    /**
     * 场景-创建
     */
    const SCENARIO_CREATE = 'create';

    /**
     * 场景-更新
     */
    const SCENARIO_UPDATE = 'update';

    /**
     * 场景-查看
     */
    const SCENARIO_VIEW = 'view';

    /**
     * 场景-删除
     */
    const SCENARIO_DELETE = 'delete';

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            [
                'class' => BlameableBehavior::class,
                'defaultValue' => 0
            ]
        ];
    }
}
