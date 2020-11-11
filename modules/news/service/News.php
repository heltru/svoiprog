<?php

namespace app\modules\news\service;


use app\modules\news\models\NewsBlock;

class News
{

    public static $url_controller = 'news';
    public static $url_module = 'news';

    public static function delete($cat_id)
    {

    }

    public static function getBlocksQuery($news_id,$block_id=null)
    {
        $query =  NewsBlock::find();
        $query->where(['news_id'=>$news_id]);
        if ($block_id){
            $query->andWhere(['id'=>$block_id]);
        }
        return $query;


    }


}