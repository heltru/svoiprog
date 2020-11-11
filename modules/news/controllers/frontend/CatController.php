<?php

namespace app\modules\news\controllers\frontend;

use app\modules\image\models\Img;
use app\modules\image\models\ImgLinks;
use app\modules\news\models\News;
use app\modules\news\models\NewsCat;
use app\modules\url\models\Url;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class CatController extends Controller
{
    public $layout = 'catalog';

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


    public function actionView($id, $url = null)
    {
        if ($url == null) {
            $url = Url::find()->where(['identity' => $id,
                'type_url' => \app\modules\news\service\NewsCat::$url_controller,
                'module' => \app\modules\news\service\NewsCat::$url_module,
                'action' => 'view'])->one();
        }

        Url::setHeaderLastMod(strtotime($url->last_mod));

        $category = NewsCat::findOne($id);
        if ($category === null) {
            throw new NotFoundHttpException('The NewsCat does not exist.');
        }

        $catItems = [];
        $catsShowContent = [];
        $news = [];

//
//
//        $catItemsQuery = Cat::find()->select(['cat.*', 'count(product.id) as count_products'])
//            ->innerJoin('product', 'product.cat_id = cat.id')
//            ->innerJoin('url', 'url.identity = product.id AND url.type_url = "product"')
//            ->innerJoin('url as urlc', 'urlc.identity = cat.id AND urlc.type_url = "catalog"')
//            ->groupBy('cat.id')
//            ->where(['cat.status' => Cat::ST_OK])
//            ->andWhere(['cat.parent_id' => $category->id]);
//
//
//        $time = new \DateTime('now');
//        $today = $time->format('Y-m-d');
//
//
//        $catsShowContent = clone $catItemsQuery;
//        $catsShowContent->andWhere(['cat.id' => $id]);
//        $catsShowContent->groupBy('cat.id,product.id');
//
//
//        $catsShowContent = $catsShowContent->all();
//
//
//        $catItems = $catItemsQuery->all();
//        $cat_query = ArrayHelper::getColumn($catItems, 'id');
//        $cat_query[] = $category->id;
//
        $news_query = News::find()->where(['news_cat_id' => $category->id]);

        $news = new ActiveDataProvider([
            'query' => $news_query,
            'pagination' => [
                'pageSize' => 100,
                'defaultPageSize' => 100,
                'forcePageParam' => false,
                'validatePage' => false
            ],
        ]);


        $this->view->params['search_results'] = [];

        //$this->view->registerJsFile('/js/main.js', ['position' => \yii\web\View::POS_END]);
        $this->view->params['curr_page'] = 'catalog';
        $this->view->params['active_cat'] = $category->id;


        $this->view->params['cats_header'] = \app\modules\news\service\NewsCat::items_cats_header();
        $this->addOpenGraph($category, $url);


        return $this->render('view', [
            'category' => $category,
            'url' => $url,
            'catItems' => $catItems,
            'catsShowContent' => $catsShowContent,
            'news' => $news

        ]);
    }

    protected function findModel($id)
    {
        if (($model = NewsCat::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    private function addOpenGraph($category, $url, $relcon = 'catalog/view')
    {
        $this->view->title = $url->title;

        $page = Yii::$app->request->get('page');
        if ((int)$page > 1) {
            $this->view->title .= ' Страница ' . $page;
        } else {
            if ($url->description_meta)
                $this->view->registerMetaTag(['name' => 'description', 'content' => $url->description_meta]);
        }
        $this->view->registerLinkTag(['rel' => 'canonical',
            'href' => $url->getRelCononical([$relcon, 'id' => $category->id], true)]);
        if ($url->keywords)
            $this->view->registerMetaTag(['name' => 'keywords', 'content' => $url->keywords]);

        $this->view->registerMetaTag(['property' => 'og:type', 'content' => 'product.group']);
        $this->view->registerMetaTag(['property' => 'og:locale', 'content' => 'ru_RU']);
        if ($url->description_meta) $this->view->registerMetaTag(['property' => 'og:description',
            'content' => $url->description_meta]);
        $this->view->registerMetaTag(['property' => 'og:title', 'content' => $url->title]);
        $this->view->registerMetaTag(['property' => 'og:url', 'content' => Yii::$app->request->hostInfo .
            \yii\helpers\Url::to([$relcon, 'id' => $category->id])]);
//$this->registerMetaTag([ 'property' => 'product:retailer_group_id', 'content' =>  $category->id . '_cat' ]);
        $imgMain = Img::getImgMain(190, ImgLinks::T_Cy, $category->id);
        if ($imgMain === null) {
            $imgMain = Img::getImgMain(300, ImgLinks::T_Cy, $category->id);
        }
        if (is_object($imgMain) && is_object($imgMain->img_r)) {
            $this->view->registerMetaTag(['property' => 'og:image', 'content' => Yii::$app->request->hostInfo .
                $imgMain->img_r->src]);
        }

    }

}
