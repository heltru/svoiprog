<?php
namespace app\modules\textpage\service;


class Textpage
{

    public static $url_controller = 'default';
    public static $url_module = 'textpage';

    public static function delete($cat_id){
        \app\modules\url\models\Url::deleteAll(['controller'=> self::$url_controller,'action'=>'view' ,'identity'=>$cat_id]);

        $allC = \app\modules\news\models\NewsCat::find()->where(['parent_id'=>$cat_id])->all();
        foreach ($allC as $c){
            $c->delete();
        }
    }
}