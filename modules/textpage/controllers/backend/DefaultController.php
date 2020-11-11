<?php

namespace app\modules\textpage\controllers\backend;


use app\modules\textpage\models\Textpage;
use app\modules\textpage\models\TextpageSearch;
use app\modules\url\models;
use app\modules\url\services\UrlService;
use Yii;
use yii\base\Model;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;


/**
 * Default controller for the `textpage` module
 */
class DefaultController extends Controller
{
    /**
     * Lists all Textpage models.
     * @return mixed
     */

    private $urlService;


    public function __construct($id, $module, UrlService $urlService, $config = [])
    {
        $this->urlService = $urlService;

        parent::__construct($id, $module, $config);
    }


    public function actionIndex()
    {

        $searchModel = new TextpageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionCreate()
    {

        $model = new Textpage();
        $url = new models\Url();


        $entitySave = false;
        if (
            $model->load(Yii::$app->request->post(), 'Textpage')
            && $url->load(Yii::$app->request->post(), 'Url')
        ) {

            if ($model->type_page == Textpage::TP_Mn) { // href is empty on mainPage
                $url->setScenario('validMainPage');
            } else {
                $url->setScenario('validHref');
            }

            if (Model::validateMultiple([$model, $url])) {
                $entitySave = $model->save();
            }
        }


        if ($entitySave) {


            $this->urlService->changePublic($url, $model->status == Textpage::ST_OK);
            $url = $this->urlService->add($url, ['controller' => \app\modules\textpage\service\Textpage::$url_controller,
                'identity' => $model->id,
                'action' => $model->type_page]);

            if ($url) {
                return $this->redirect(['update', 'id' => $model->id]);
            }

        }

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


    /**
     * Updates an existing Textpage model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {

        $model = $this->findModel($id);

        $url = $this->urlService->findUrl(['controller' =>\app\modules\textpage\service\Textpage::$url_controller,
            'module' => \app\modules\textpage\service\Textpage::$url_module,
            'identity' => $id ]);

        $entitySave = false;

        if (
            $model->load(Yii::$app->request->post(), 'Textpage')
            && $url->load(Yii::$app->request->post(), 'Url')
        ) {

            if ($model->type_page == Textpage::TP_Mn) { // href is empty on mainPage
                $url->setScenario('validMainPage');
            } else {
                $url->setScenario('validHref');
            }

            if (Model::validateMultiple([$model, $url])) {
                $entitySave = $model->save();
            }

        }


        if ($entitySave) {

            $this->urlService->changePublic($url, $model->status == Textpage::ST_OK);

            $url = $this->urlService->add($url, ['controller' => $model->module, 'identity' => $model->id,
                'action' => $model->type_page]);
            if ($url) {
                return $this->redirect(['update', 'id' => $model->id]);
            }

        }

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

    public function actionValidateTextpage()
    {
        $model = new Textpage();

        $url = new models\Url();


        $request = \Yii::$app->getRequest();
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($request->isPost &&
            $model->load($request->post(), 'Textpage') &&
            $url->load(Yii::$app->request->post(), 'Url')
        ) {
            if ($model->type_page == Textpage::TP_Mn) {
                $url->setScenario('validMainPage');
            } else {
                $url->setScenario('ajaxValid');
            }


            return ActiveForm::validateMultiple([$model, $url]);
        }
    }


    public function actionChangeStatus()
    {
        $id = (int)Yii::$app->request->post()['id'];
        $tp = Textpage::findOne(['id' => $id]);

        if ($tp !== null) {
            ($tp->status == Textpage::ST_OK) ? $tp->status = Textpage::ST_NO : $tp->status = Textpage::ST_OK;

            $tp->update(false, ['status']);
            return 1;
        }

    }

    /**
     * Deletes an existing Textpage model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Textpage model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Textpage the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Textpage::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


}
