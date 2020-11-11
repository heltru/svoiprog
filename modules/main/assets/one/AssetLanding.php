<?php

namespace app\modules\main\assets\one;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class  AssetLanding extends AssetBundle
{
    public $basePath = '@webroot/themes/one';
    public $baseUrl = '@web/themes/one';

    public $css = [
      //  'css/css.css',
        //'https://fonts.googleapis.com/icon?family=Material+Icons',
        '/css/google-matrial-icon.css',
        'css/materialize.css',
      //  'css/style.css',
        'css/timer.css',

    ];
    public $js = [
        'scripts/jquery-3.2.1.min.js',
        'js/materialize.js',
        'js/init.js',
        'scripts/timer.js'
    ];
    public $jsOptions = ['position' => \yii\web\View::POS_BEGIN];




}
