<?php
/**
 * Initialize the HumHub Application for functional testing. The default application configuration for this suite can be overwritten
 * in @tests/config/functional.php
 */
$config = require(dirname(__DIR__) . '/config/functional.php');
new humhub\components\Application($config);

$cfg = \Codeception\Configuration::config();

if(!empty($cfg['humhubModules'])) {
    Yii::$app->moduleManager->enableModules($cfg['humhubModules']);
}