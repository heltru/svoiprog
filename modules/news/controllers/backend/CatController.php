<?php

namespace app\modules\news\controllers\backend;

use app\assets\AppAsset;
use app\modules\news\models\NewsCat;
use app\modules\news\models\NewsCatSearch;
use app\modules\url\models\Url;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class CatController extends Controller
{

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }


    public function actionIndex()
    {
        $searchModel = new NewsCatSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionCreate()
    {

        $model = new NewsCat();
        $url = new Url();
        $url->pagination = 1; // cat
        $url->setScenario('validHref');
        $valid = false;


        $this->view->registerJsFile("/js/news/Cat.js", ['depends' => [AppAsset::class]]);
        $this->view->registerJsFile("/js/url/Url.js", ['depends' => [AppAsset::class]]);

        if ($model->load(Yii::$app->request->post()) &&
            $url->load(Yii::$app->request->post(), 'Url')) {
            $url->addWithPreHref();

            if ($url->validate() && $model->validate()) {

                $transaction = Yii::$app->db->beginTransaction();
                try {

                    $u = $url->save();
                    $p = $model->save();

                    $action = 'view';
                    if ($model->parent_id != 0) { // subcat cat => action = viewsubcat
                        $action = 'viewsubcat';
                    }
                    $url->setUrlLink($model, \app\modules\news\service\NewsCat::$url_controller, $action,   \app\modules\news\service\NewsCat::$url_module);
                    $url->update(false, ['controller', 'identity', 'action','module']);
                    $model->beforeSave(false);


                    if ($p && $u) {
                        $valid = true;
                    }

                    $transaction->commit();
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    throw $e;
                }


            }
        }
        if ($valid) {
            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            foreach ($model->getErrors() as $attr => $error) {
                Yii::$app->session->setFlash('danger', $error[0]);
            }
            foreach ($url->getErrors() as $attr => $error) {
                Yii::$app->session->setFlash('danger', $error[0]);
            }

            return $this->render('create', [
                'model' => $model,
                'url' => $url,

            ]);
        }


    }


    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $url = Url::findOne(['identity' => $id, 'controller' => \app\modules\news\service\NewsCat::$url_controller]);
        if (null === $url) {
            $url = new Url();
        }

        $url->setScenario('validHref');
        $valid = false;

        $this->view->registerJsFile("/js/news/Cat.js", ['depends' => [AppAsset::class]]);
        $this->view->registerJsFile("/js/url/Url.js", ['depends' => [AppAsset::class]]);

        if ($model->load(Yii::$app->request->post()) &&
            $url->load(Yii::$app->request->post(), 'Url')) {

            $url->addWithPreHref();

            if ($url->validate() && $model->validate()) {

                $transaction = Yii::$app->db->beginTransaction();
                try {

                    $m = $model->save();

                    //if ($url->isNewRecord) {
                    $action = 'view';
                    if ($model->parent_id != 0) { // subcat cat => action = viewsubcat
                        $action = 'viewsubcat';
                    }
                    $url->setUrlLink($model, \app\modules\news\service\NewsCat::$url_controller, $action,  \app\modules\news\service\NewsCat::$url_module);
                    //   }

                    $u = $url->save();

                    if ($m && $u) {
                        $valid = true;
                    }

                    $transaction->commit();
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    throw $e;
                }


            }


        }

        if ($valid) {
            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            foreach ($model->getErrors() as $attr => $error) {
                Yii::$app->session->setFlash('danger', $error[0]);
            }
            foreach ($url->getErrors() as $attr => $error) {
                Yii::$app->session->setFlash('danger', $error[0]);
            }

            return $this->render('update', [
                'model' => $model,
                'url' => $url,
            ]);
        }
    }

    public function actionDelete($id)
    {

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->findModel($id)->delete();
            \app\modules\news\service\NewsCat::delete($id);

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = NewsCat::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
