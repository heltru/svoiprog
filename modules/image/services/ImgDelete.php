<?php


namespace app\modules\image\services;


use app\modules\image\models\Img;
use app\modules\image\models\ImgLinks;
use Yii;

class ImgDelete
{
    public $id;


    public function begin(){

        $this->delete($this->id);
    }
    private function delete($id){


        $model = $this->findModel($id);
        $imgLink = ImgLinks::findOne(['id_image'=>$model->id]);
        $fp = $model->name_image;



        if ( $model->parent_id ){ //del child
            $models = Img::findAll(['parent_id'=>$model->parent_id]);
            foreach ($models as $model){

                $imgLink = ImgLinks::findOne(['id_image'=>$model->id]);

                $fp = $model->name_image;
                if ( file_exists($fp)) {
                    @unlink($fp);
                }

                $model->delete();
                $imgLink->delete();
            }

            $model = $model->parent_r;
            if (is_object($model)){

                $imgLink = ImgLinks::findOne(['id_image'=>$model->id]);

                $fp = $model->name_image;
                if ( file_exists($fp)) {
                    @unlink($fp);
                }

                $model->delete();
                $imgLink->delete();
            }

        } else {

            //del child
            $delImg = Img::find()->where(['parent_id'=>$model->id])->all();
            foreach ($delImg as $img){
                $fpc = $img->name_image;
                if ( file_exists($fp)) {
                    @unlink($fpc);
                }
                ImgLinks::findOne(['id_image'=>$img->id])->delete();
                $img->delete();
            }
            //del par
            if ( file_exists($fp)) {

                @unlink($fp);
            }
            $model->delete();
            $imgLink->delete();
        }

    }
}