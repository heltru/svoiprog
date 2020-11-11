<?php

namespace app\modules\textpage\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class  AssetMain extends AssetBundle
{

    public $basePath = '@webroot/themes/news';
    public $baseUrl = '@web/themes/news';
    public $css = [

        'styles/style.css',
        'fancy/jquery.fancybox.min.css',
        'styles/slick-slider.css',

    ];
    public $js = [
        'scripts/jquery.js',

    ];
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
    public $depends = [
       // 'yii\web\YiiAsset',
    ];


}
