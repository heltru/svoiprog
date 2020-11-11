<?php

namespace app\modules\image\controllers\frontend;


use app\modules\image\models\Img;
use yii\web\Controller;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;


class DefaultController extends Controller
{

    public function actionOrigImg(){
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $idImg = \Yii::$app->request->post('id');
        if ($idImg){
            $img = Img::findOne(['id'=>$idImg]);


            $imgF = $img->getFullSizeItem();

            if ( $imgF !== null){
                return  ['status'=>'200',
                    'data'=> $imgF->name_image,
                    'title' => $img->title
                ];
            } else {
                if ($img !== null && is_object($img->parent_r)){

                    return  ['status'=>'200',
                        'data'=> $img->parent_r->name_image,
                        'title' => $img->title
                    ];
                }
            }



        }

        return    ['status'=>'404'] ;
    }

}
