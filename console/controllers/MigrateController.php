<?php
namespace console\components;

/**
 * Class MigrateController
 * @package console\components
 */
class MigrateController extends \yii\console\controllers\MigrateController
{
    public $migrationTable = '{{%cc_migration}}';
}