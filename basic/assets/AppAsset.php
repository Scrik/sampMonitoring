<?php

namespace app\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle {
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        //'css/bootstrap.css',
        //'css/bootstrap.min.css',
        'css/blog-post.css',
    ];
    public $js = [
		//'js/bootstrap.js',
		//'js/bootstrap.min.js',
		//'js/jquery.js',
    ];
    public $depends = [
    ];
}