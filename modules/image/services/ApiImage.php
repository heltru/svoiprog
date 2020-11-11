<?php


namespace app\modules\image\services;


use app\modules\image\models\ImgLinks;
use Yii;

class ApiImage
{

    public function getImages($query){
        $imgs = [];

        if ($query['q_type'] == 'entity'){
            $id_type = $query['id_type'];
            $type = $query['type'];

            $img_link = ImgLinks::find()->where(['type'=>$type,'id_type'=>$id_type])
                ->joinWith(['img_r']);
            if (isset($query['original'])){
                $img_link ->andWhere(['img.original'=>(int)$query['original']]);
            }
            if (isset($query['fullsize'])){
                $img_link ->andWhere(['img.fullsize'=>(int)$query['fullsize']]);
            }

            $img_link->orderBy(['img.width'=>SORT_DESC])->all();

            $img_link = $img_link->all();



            foreach ($img_link as $link){
                if (! is_object($link)) continue;
                $link->img_r->name_image = Yii::$app->request->hostInfo . '/' . $link->img_r->name_image;
                $imgs[] =  $link->img_r;
            }

        }


        return $imgs;
    }

}