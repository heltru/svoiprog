<?php

namespace app\modules\url\controllers\backend;

use app\modules\product\models\Product;
use Yii;
use app\modules\url\models\UrlRedirect;
use app\modules\url\models\UrlRedirectSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UrlRedirectController implements the CRUD actions for UrlRedirect model.
 */
class UrlRedirectController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all UrlRedirect models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UrlRedirectSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionImport(){

        if ( isset(  $_FILES ) &&
            isset($_FILES['filedata']) && file_exists($_FILES['filedata']['tmp_name'])
        ){

            $csv = array_map('str_getcsv', file($_FILES['filedata']['tmp_name']));

            if (is_array($csv)) {

                foreach ($csv as $num => $row) {
                 //   if ($num == 0) continue;;
                    $col_one = $row[0];
                    $pdata =   parse_url($col_one);
                    if ($pdata['path'] == '/') continue;
                    $trimUrl =  ltrim($pdata['path'],'/');
                    $urlParts = explode('/',$trimUrl);


                    $ins = 0;

                    if ( count($urlParts)  == 2){
                        $extCatName = $urlParts[0];
                        $extProdName = $urlParts[1];

                        $extCatUrl  = Yii::$app->dbgh->createCommand(
                            'SELECT * FROM oc_url_alias WHERE keyword = :nb' ,['nb'=>$extCatName])->queryOne();

                        $extProdUrl  = Yii::$app->dbgh->createCommand(
                            'SELECT * FROM oc_url_alias WHERE keyword = :nb' ,['nb'=>$extProdName])->queryOne();


                        if ( ! ($extCatUrl === false || $extProdUrl === false) ){
                            $parCat = explode('=',$extCatUrl['query']);
                            $parProd = explode('=',$extProdUrl['query']);


                            if ( (count($parCat   )== 2 && $parCat[0] == 'category_id' )
                                && (count($parProd   )== 2  && $parProd[0] == 'product_id' )
                            ){
                                $prod_ext_id = $parProd[1];

                                $extProdd  = Yii::$app->dbgh->createCommand(
                                    'SELECT * FROM oc_product WHERE product_id = :nb' ,['nb'=>$prod_ext_id])->queryOne();

                                $extIdcrm = ($extProdd['model'] ) ? $extProdd['model'] : $extProdd['sku'];

                                if ( $extIdcrm !== false) {
                                    $prod = Product::findOne(['id_crm'=>$extIdcrm]);
                                    if ($prod !== null){

                                        $url =  $prod->url_rr;
                                        $rec = new UrlRedirect();
                                        $rec->url_in = $trimUrl;
                                        $rec->url_out = 'https://kirov.gradushaus.ru/' . $url->rawHref;
                                        $rec->save();
                                        $ins = 1;

                                    }
                                }
                            }
                        }

                    }

                    if ( $ins == 0){
                        $rec = new UrlRedirect();
                        $rec->url_in = $trimUrl;
                        $rec->url_out = 'https://kirov.gradushaus.ru';
                        $rec->save();
                    }

                }
            }


        }


        return $this->render('_import_file' );
    }


    public function actionMakeRedirect($url){
        //kopchenie/dymogeneratory/bravo-favorit

        header("Location: $url",TRUE,302);
        Yii::$app->end();
        
    }

    /**
     * Displays a single UrlRedirect model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new UrlRedirect model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UrlRedirect();


        Yii::$app->db->schema->refresh(); // remove all loaded from cache



        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing UrlRedirect model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing UrlRedirect model.
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
     * Finds the UrlRedirect model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UrlRedirect the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UrlRedirect::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
