<?php

namespace app\modules\url\controllers\backend;


use app\modules\url\models\Url;
use yii\web\Controller;
use app\modules\url\models\UrlSearch;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * Default controller for the `url` module
 */
class DefaultController extends Controller
{
    /**
     * Lists all Url models.
     * @return mixed
     */
    public function actionIndex()
    {


        $searchModel = new UrlSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $model->setScenario('validHref');
        $post = Yii::$app->request->post();



        if ($model->load($post)){

            if (isset($post['Url']) && isset($post['Url']['rawHref'])){
                $model->href = $post['Url']['rawHref'];
            }


            $res = $model->update(false,['href','real_canonical','title','h1','description_meta','redirect']);

            if ($res){

                return $this->redirect(['update', 'id' => $model->id]);

            } else {

                foreach ($model->getErrors() as $attr => $error){
                    Yii::$app->session->setFlash('danger', $error[0]);
                }
            }


        }


        return $this->render('update', [
            'model' => $model,
        ]);

    }



    public function actionRemoveRedirect($id){
        $url_d = Url::findOne(['id'=>$id]);
        if ( $url_d !== null){
            $url_d->delete();

            $url_r = Url::findOne(['redirect'=>$url_d->id]);
            if ($url_r !== null){
                $url_r->redirect = 0;
                $url_r->update(false,['redirect']);
            }
        }
        return $this->redirect(Yii::$app->request->referrer);

    }


    protected function findModel($id)
    {
        if (($model = Url::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Deletes an existing Url model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        $a = Url::findOne(['redirect'=>$model->id]);



        if ($a !== null /*&& $model->redirect*/){

            if ($model->redirect){
                $a->redirect = $model->redirect;
                $a->update(false,['redirect']);
            } else {
                $a->redirect = 0;
                $a->update(false,['redirect']);
            }


        }

        return $this->redirect(['index']);
    }


}
