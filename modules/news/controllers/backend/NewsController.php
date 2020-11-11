<?php

namespace app\modules\news\controllers\backend;


use app\assets\AppAsset;
use app\modules\image\models\ImgLinksSearch;
use app\modules\image\services\AttImg;
use app\modules\news\models\News;
use app\modules\news\models\NewsBlock;
use app\modules\news\models\NewsBlockSearch;
use app\modules\news\models\NewsSearch;
use app\modules\url\models\Url;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\Response;
use yii\widgets\ActiveForm;


class NewsController extends Controller
{
    private $attImg = null;

    public function init()
    {
        $this->attImg = new AttImg();
        parent::init();
    }

    public function beforeAction($action)
    {
        if (in_array($action->id, ['block-editor-detail', 'block-editor-save'])) {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }

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

        $searchModel = new NewsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new News();
        $model->date_public = time();
        $model->type = News::T_OWN;
        $model->first = News::F_NO;
        $model->status = News::ST_OK;

        $url = new Url();
        //$url->date_public = time();
        $url->setScenario('validHref');

        $searchModelImgLinks = new ImgLinksSearch();
        $dataProviderImgLinks = $searchModelImgLinks->search(Yii::$app->request->queryParams, $model->id, 'news');


        $this->view->registerJsFile("/js/news/News.js", ['depends' => [AppAsset::class]]);
        $this->view->registerJsFile("/js/url/Url.js", ['depends' => [AppAsset::class]]);

        $valid = false;
        if ($model->load(Yii::$app->request->post(), 'News') &&
            $url->load(Yii::$app->request->post(), 'Url')) {
            if ($model->validate() && $url->validate()) {

                $model->ord = (int)News::find()->count();

                $u = $url->save();
                $p = $model->save();

                $url->setUrlLink($model,
                    \app\modules\news\service\News::$url_controller,
                    $action = 'view',
                    \app\modules\news\service\News::$url_module
                );
                $url->update(false, ['controller', 'identity', 'action']);
                $model->beforeSave(false);


                if ($p && $u) {
                    $this->attImg->preseachImgNew('news', $model->id);
                    $valid = true;
                }
            }
            if ($valid) {
                return $this->redirect(\yii\helpers\Url::to(['update', 'id' => $model->id]));
            } else {

                foreach ($model->getErrors() as $attr => $error) {
                    Yii::$app->session->setFlash('danger', $error[0]);
                }
                foreach ($url->getErrors() as $attr => $error) {
                    Yii::$app->session->setFlash('danger', $error[0]);
                }


            }
        }
        return $this->render('create', [
            'model' => $model, 'url' => $url, 'dataProviderImgLinks' => $dataProviderImgLinks
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $url = $this->findUrl($id);

        $url->setScenario('validHref');

        $searchProdDescr = new NewsBlockSearch();
        $dpDescr = $searchProdDescr->search(Yii::$app->request->queryParams, $model->id);

        $searchModelImgLinks = new ImgLinksSearch();
        $dataProviderImgLinks = $searchModelImgLinks->search(Yii::$app->request->queryParams, $model->id, 'news');

        //  $this->view->registerCssFile('/themes/news/styles/style.css?14');

        /*
         *   $this->attImg->preseachImgNew('news', $model->id);
         *  return $this->render('update', [
                'dataProviderImgLinks'=>$dataProviderImgLinks,
                'model' => $model,
                'dpDescr'=>$dpDescr
            ]);
         *
         * */
        $this->view->registerJsFile("/js/news/News.js", ['depends' => [AppAsset::class]]);
        $this->view->registerJsFile("/js/url/Url.js", ['depends' => [AppAsset::class]]);

        $valid = false;
        if ($model->load(Yii::$app->request->post(), 'News') &&
            $url->load(Yii::$app->request->post(), 'Url')) {
            if ($model->validate() && $url->validate()) {

                if ($url->isNewRecord)
                    $url->setUrlLink($model, 'news');

                if ($model->save() && $url->save()) {
                    $this->attImg->preseachImgNew('news', $model->id);
                    $valid = true;
                }
            }


            if ($valid) {
                return $this->redirect(\yii\helpers\Url::to(['update', 'id' => $model->id]));
            } else {
                foreach ($model->getErrors() as $attr => $error) {
                    Yii::$app->session->setFlash('danger', $error[0]);
                }
                foreach ($url->getErrors() as $attr => $error) {
                    Yii::$app->session->setFlash('danger', $error[0]);
                }

            }

        }

        return $this->render('update', [
            'dataProviderImgLinks' => $dataProviderImgLinks,
            'model' => $model,
            'dpDescr' => $dpDescr,
            'url' => $url,
        ]);


    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionNewsSort()
    {
        $sendArr = Yii::$app->request->post()['info'];

        $explA = explode('&', $sendArr);
        $res = [];
        if (is_array($explA)) {
            foreach ($explA as $p => $item) {
                $explI = explode('=', $item);
                if (count($explI) > 1) {
                    $id = (int)$explI[1];
                    // $this->db->query(" UPDATE c_pages SET sort = ".$p." WHERE pid = ".$id." ");
                    \Yii::$app->db->createCommand("UPDATE news SET ord =:ord WHERE id=:id")
                        ->bindValue(':ord', $p)->bindValue(':id', $id)
                        ->execute();
                    //echo 'UPDATE';

                }
            }
        }

    }

    public function actionChangeStatus()
    {
        $id = (int)Yii::$app->request->post()['id'];
        $cat = News::findOne(['id' => $id]);
        if ($cat !== null) {
            ($cat->status == News::ST_OK) ? $cat->status = News::ST_NO : $cat->status = News::ST_OK;
            $cat->update(false, ['status']);
            return 1;
        }

    }

    public function actionValidate()
    {
        $model = new News();

        $url = new Url();


        $request = \Yii::$app->getRequest();
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($request->isPost &&
            $model->load($request->post(), 'News') &&
            $url->load(Yii::$app->request->post(), 'Url')
        ) {
//            if ($model->type_page == Textpage::TP_Mn){
//                $url->setScenario('validMainPage');
//            } else {
//                $url->setScenario('ajaxValid');
//            }


            return ActiveForm::validateMultiple([$model, $url]);
        }
    }

    protected function findUrl($action_id) //id action
    {
        if (($prUrl = Url::findOne(['identity' => $action_id, 'controller' => 'news'])) !== null) {
            return $prUrl;
        } else {
            return new Url();
        }

        //throw new NotFoundHttpException('The url by product page does not exist.');
    }

    protected function findModel($id)
    {
        if (($model = News::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionNewsDescrAdd($id = null)
    { //$id product

        $model = new NewsBlock();

        if ($id !== null) $model->news_id = $id;


        if ($model->load(Yii::$app->request->post())) {

            $model->ord = NewsBlock::find()->where(['news_id' => $id])->count();


            if ($model->save()) {

                $this->attImg->preseachImgNew('news_bl', $model->id);
                return $this->redirect(
                    \yii\helpers\Url::to(['update', 'id' => $model->news_id, '#' => 'tab12'])
                );
            }


        }

        foreach ($model->getErrors() as $attr => $error) {
            Yii::$app->session->setFlash('danger', $error[0]);
        }

        $searchModelImgLinks = new ImgLinksSearch();
        $dataProviderImgLinks = $searchModelImgLinks->search(Yii::$app->request->queryParams, $model->id, 'news_bl');

        return $this->render('include/block', [
            'model' => $model,
            'dataProviderImgLinks' => $dataProviderImgLinks,
        ]);

    }

    public function actionNewsDescrSort()
    {
        $sendArr = Yii::$app->request->post()['info'];

        $explA = explode('&', $sendArr);
        $res = [];
        if (is_array($explA)) {
            foreach ($explA as $p => $item) {
                $explI = explode('=', $item);
                if (count($explI) > 1) {
                    $id = (int)$explI[1];
                    // $this->db->query(" UPDATE c_pages SET sort = ".$p." WHERE pid = ".$id." ");
                    \Yii::$app->db->createCommand("UPDATE news_block SET ord =:ord WHERE id=:id")
                        ->bindValue(':ord', $p)->bindValue(':id', $id)
                        ->execute();
                    //echo 'UPDATE';

                }
            }
        }

    }


    public function actionNewsDescrUpdate($id)
    {

        $model = NewsBlock::find()->where(['id' => $id])->one();

        $searchModelImgLinks = new ImgLinksSearch();
        $dataProviderImgLinks = $searchModelImgLinks->search(Yii::$app->request->queryParams, $model->id, 'news_bl');

        if ($model !== null) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                $this->attImg->preseachImgNew('news_bl', $model->id);

                return $this->redirect(['news-descr-update', 'id' => $model->id]);
            }


        }

        return $this->render('include/block', [
            'model' => $model,
            'dataProviderImgLinks' => $dataProviderImgLinks,
        ]);

    }

    public function actionNewsDescrDel()
    {
        $id = (int)Yii::$app->request->post()['id'];
        $model = NewsBlock::findOne(['id' => $id]);
        if ($model !== null) {
            $model->delete();
        }
    }

    public function actionNewsDescrChangeStatus()
    {
        $id = (int)Yii::$app->request->post()['id'];
        $com = NewsBlock::findOne(['id' => $id]);
        if ($com !== null) {
            $com->status = (int)!$com->status;
            $com->update(false, ['status']);
        }
    }

    public function actionNewsDescrValidate()
    {
        $model = new NewsBlock();

        $request = \Yii::$app->getRequest();
        if ($request->isPost && $model->load($request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);

        }
    }

    public function actionBlockEditorDetail()
    {

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;


        $response['success'] = true;

        try {

            $params = [
                'block_id' => isset($_REQUEST['block_id']) ? (int)($_REQUEST['block_id']) : '',
            ];


            $block = NewsBlock::find()->where(['id' => $params['block_id']])->one();
            if ($block === null) {
                throw  new \Exception('NewsBlock not found');
            }

            $response['item'] = $block->toArray();
            $response['item']['type_block'] = Html::activeDropDownList($block, 'type_block', NewsBlock::$arrTxtBlock, ['prompt' => 'Выберите тип блока']);


        } catch (\Throwable $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();

        }

        return $response;

    }

    public function actionBlockEditorSave()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;


        $response['success'] = true;

        try {


            $params = [
                'block_id' => isset($_REQUEST['NewsBlock']) && isset($_REQUEST['NewsBlock']['id']) ? $_REQUEST['NewsBlock']['id'] : '',
            ];


            $block = NewsBlock::find()->where(['id' => $params['block_id']])->one();
            if ($block === null) {
                throw  new \Exception('NewsBlock not found');
            }

            $block->load(Yii::$app->request->post(), 'NewsBlock');

            $block->update();

            if ($block->getErrors()) {
                throw  new \Exception(print_r($block->getErrors(), 1));
            }



        } catch (\Throwable $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();

        }

        return $response;
    }

}