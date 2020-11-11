<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class LandingAsset extends AssetBundle
{
    public $basePath = '@webroot/themes/parallax-template';
    public $baseUrl = '@web/themes/parallax-template';
    public $css = [
        'css/materialize.min.css',
        'css/style.css'

    ];
    public $js = [
        'scripts/jquery-3.2.1.min.js',
        'materialize.min.js',
        'init.js',

    ];
    public $jsOptions = ['position' => \yii\web\View::POS_BEGIN];

}
