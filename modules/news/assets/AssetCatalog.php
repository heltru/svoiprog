<?php

namespace app\modules\news\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class  AssetCatalog extends AssetBundle
{
    public $basePath = '@webroot/themes/news';
    public $baseUrl = '@web/themes/news';
    public $css = [
        'styles/style.css',
    ];
    public $js = [
        'scripts/jquery.js',
    ];
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];



}
