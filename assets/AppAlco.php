<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAlco extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/admin.css',
        'css/ionicons.min.css',
        'plugins/select2/css/select2.css'
        //    <link rel="stylesheet" href="/web/plugins/select2/css/select2.min.css">

    ];

    public $js = [
         //'js/main.js',

        'plugins/select2/js/select2.js',
          'js/common.js',
      //  'plugins/datetimepicker/jquery.datetimepicker.js'
    ];




    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];

}
