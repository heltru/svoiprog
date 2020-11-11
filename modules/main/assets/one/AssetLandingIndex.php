<?php

namespace app\modules\main\assets\one;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class  AssetLandingIndex extends AssetBundle
{
    public $basePath = '@webroot/themes/one';
    public $baseUrl = '@web/themes/one';

    public $css = [
        'css/index.css',
        'https://fonts.googleapis.com/icon?family=Material+Icons',

        'css/materialize.css',
        'css/style.css',

    ];
    public $js = [
        'scripts/jquery-3.2.1.min.js',
        'js/materialize.js',
        'js/init.js'
    ];
    public $jsOptions = ['position' => \yii\web\View::POS_BEGIN];




}
