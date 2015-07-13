<?php

defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'prod');

require(__DIR__ . '/basic/vendor/autoload.php');
require(__DIR__ . '/basic/vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/basic/config/web.php');

(new yii\web\Application($config))->run();