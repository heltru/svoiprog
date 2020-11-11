<?php

namespace app\modules\news\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class  AssetInfoPage extends AssetBundle
{
    public $basePath = '@webroot/themes/news';
    public $baseUrl = '@web/themes/news';
    public $css = [
        'styles/style.css?14',
        'fancy/jquery.fancybox.min.css',
        'styles/slick-slider.css'

    ];
    public $js = [
        'scripts/jquery.js',
        'scripts/info.js',
        'scripts/general.js?1',
        'scripts/delivery.js',
        'scripts/slick-slider.js',
        'fancy/jquery.fancybox.min.js',
        'scripts/jquery.maskedinput.min.js'

    ];
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
    public $depends = [
        //'yii\web\YiiAsset',
    ];


}
