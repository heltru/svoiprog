<?php

namespace app\modules\textpage\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AssetMainEnd extends AssetBundle
{

    public $basePath = '@webroot/themes/news';
    public $baseUrl = '@web/themes/news';
    public $css = [

    ];
    public $js = [
        'scripts/jquery.maskedinput.min.js',
        'scripts/general.js',
        'scripts/main.js',
        'scripts/slick-slider.js',
        'fancy/jquery.fancybox.min.js'
    ];
    public $jsOptions = ['position' => \yii\web\View::POS_END];


}
