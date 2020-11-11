<?php

namespace app\modules\admin\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class  AssetAdminEnd extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'fancy/jquery.fancybox.css'
    ];
    public $js = [
        'fancy/jquery.fancybox.min.js',
        'scripts/general.js',
        'scripts/campaign.js',
        'scripts/jquery.maskedinput.min.js'
    ];
    public $jsOptions = ['position' => \yii\web\View::POS_END];
    public $depends = [
       // 'yii\web\YiiAsset',
      //  'yii\bootstrap\BootstrapAsset',
    ];

}
