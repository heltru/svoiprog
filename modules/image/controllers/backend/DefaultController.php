<?php

namespace app\modules\image\controllers\backend;

use app\modules\image\models\ImgLinksSearch;
use app\modules\image\services\AttImg;
use app\modules\product\models\Product;
use yii\web\Controller;
use app\modules\image\models\ImgLinks;
use app\modules\image\models\Img;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Default controller for the `image` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */

    private $attImg = null;


    public function __construct($id, $module, \app\modules\image\services\AttImg $attImg, $config = [])
    {
        $this->attImg = $attImg;

        parent::__construct($id, $module, $config);
    }


    public function actionIndex()
    {
        $searchModelImgLinks = new ImgLinksSearch();
        $dataProviderImgLinks = $searchModelImgLinks->search(Yii::$app->request->queryParams,0,ImgLinks::T_Wm);


        $this->attImg->preseachImgNew(ImgLinks::T_Wm,0);

        return $this->render('index',['dataProviderImgLinks'=>$dataProviderImgLinks]);
    }

    public function actionAjaxImgSort()
    {

        //input id_img_link - id_orig_image
        // find all childs original
        // change ord

        $sendArr = Yii::$app->request->post('ids');
        $ids = explode(',',$sendArr);

        if (count($ids))
        {

            foreach ($ids as $num => $id){
                $img = Img::findOne(['id'=>(int)$id]);
                if ($img !== null){
                    $img->ord = (int) $num;
                    $img->update(false,['ord']);
                }
            }
        }

    }

    public function actionEntImgDeleteAll(){

        $ent_id = (int)Yii::$app->request->post('ent_id');
        $ent_type = Yii::$app->request->post('ent_type');


        foreach ( ImgLinks::findAll(['id_type'=>$ent_id,'type'=>$ent_type]) as $imgLink){
            $model =  $imgLink->img_r;

            if ( is_object($model)){
                $fp = $model->name_image;

                if ( file_exists($fp)) {
                    @unlink($fp);
                }
                $model->delete();
                $imgLink->delete();

            }

        }


    }


    private function delImgLinkFull($imgLink,$model){

        $fp = $model->name_image;

        if ( $model->parent_id ){ //del child
            if ( file_exists($fp)) {
                @unlink($fp);
            }

            $imgP = Img::findOne(['id'=>$model->parent_id]);
            if ($imgP!== null){
                @unlink($imgP->name_image);
                $imgP->delete();
            }

            $imgLinkP = ImgLinks::findOne(['id_image'=>$model->parent_id ]);
            $imgLinkP->delete();
            //@unlink($model->parent_r->name_image);

            //$model->parent_r->delete();
            $model->delete();
            $imgLink->delete();

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

    public function actionDelete($id)
    {
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

        return $this->redirect(Yii::$app->request->referrer);


    }

    public function actionAjaxDelete()
    {
        $id = Yii::$app->request->post('id');

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


    public function actionOptimizeAjax(){

        $attimg = new AttImg();

        Yii::$app->response->format = Response::FORMAT_JSON;

        if ( isset($_FILES['imgpreview']) &&
             $_FILES['imgpreview']['error'] == UPLOAD_ERR_OK )  {

            if (  is_file( $_FILES['imgpreview']['tmp_name'] )  ){

        /*        return ['status'=>'200',
                    'response'=>
                        [
                            'src'=>'data:'.$_FILES['imgpreview']['tmp_name'] .';base64,'.
                                base64_encode(file_get_contents($_FILES['imgpreview']['tmp_name'])),
                            'newsize'=>123
                        ]
                ];
*/
                $temp_file = tempnam(ini_get('upload_tmp_dir'), 'optimg');

                $res =  $attimg->optimizeImg( $_FILES['imgpreview']['tmp_name'] , $temp_file);


                if ($res){
                    $a =  file_get_contents($temp_file);
                    return ['status'=>'200',
                        'response'=>
                        [
                            'src'=>'data:'.$_FILES['imgpreview']['type'] .';base64,'. base64_encode($a),
                            'newsize'=>$res
                        ]
                    ];
                }
            }


        }
        return ['status'=>'500','response'=>''];
    }

    protected function findModel($id)
    {
        if (($model = Img::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
