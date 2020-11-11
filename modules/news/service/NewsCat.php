<?php

namespace app\modules\news\service;


class NewsCat
{

    public static $url_controller = 'cat';
    public static $url_module = 'news';

    public static function delete($cat_id)
    {
        \app\modules\url\models\Url::deleteAll(['controller' => self::$url_controller, 'action' => 'view', 'identity' => $cat_id]);


        $allC = \app\modules\news\models\NewsCat::find()->where(['parent_id' => $cat_id])->all();
        foreach ($allC as $c) {
            $c->delete();
        }
    }

    public static function itemsListTree($all = true)
    {

        $cat = \app\modules\news\models\NewsCat::find()->orderBy('parent_id,ord');

        $cat = $cat->all();
        $arr = [];
        foreach ($cat as $item) {
            if ($item->parent_id != 0) {
                $arr[$item->id] = $item->parentCat_r->name . ' > ' . $item->name;
            } else {
                $arr[$item->id] = $item->name;
            }

        }
        return $arr;
    }

    public static function items_cats_header()
    {

        return \app\modules\news\models\NewsCat::find()->where(['parent_id' => 0])
            ->orderBy('ord')->all();
    }


}