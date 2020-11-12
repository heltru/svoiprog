<?php

namespace app\modules\news\controllers\frontend;


use app\assets\AppAsset;
use app\modules\image\models\Img;
use app\modules\image\models\ImgLinks;
use app\modules\news\assets\AssetInfoPage;
use app\modules\news\models\News;
use app\modules\url\models\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;


class NewsController extends Controller
{

    public function actionView($id, $url = null)
    {

        if ($url == null) {
            $url = Url::find()->
            where(['identity' => $id,
                'controller' =>\app\modules\news\service\News::$url_controller,
                'module' => \app\modules\news\service\News::$url_module,
                'action' => 'view'])->one();
        }

        Url::setHeaderLastMod(strtotime($url->last_mod));

        $this->layout = 'infopage';

        $news = $this->findModel($id);
        $news_blocks_query = \app\modules\news\service\News::getBlocksQuery($id);
        $news_blocks = $news_blocks_query->all();


        $this->view->params['search_results'] = [];
        $this->view->params['cats_header'] = \app\modules\news\service\NewsCat::items_cats_header();
        $this->view->params['curr_page'] = 'action';

        $this->view->registerJsFile("/js/news/NewsBlocksEditor.js", ['depends' => [AssetInfoPage::class]]);
        $this->view->registerJsFile("/plugins/ckeditor/ckeditor.js", ['depends' => [AssetInfoPage::class]]);
        $this->view->registerJsFile("/plugins/ckeditor/adapters/jquery.js", ['depends' => [AssetInfoPage::class]]);


        $this->addOpenGraph($news, $url);

        return $this->render('view', [
            'news' => $news,
            'url' => $url,
            'news_blocks' => $news_blocks
        ]);
    }

    protected function findModel($id)
    {
        if (($model =  News::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    private function addOpenGraph($blog,$url){

        $this->view->title = $url->title;
        if ($url->description_meta)  $this->view->registerMetaTag([ 'name' => 'description',
            'content' =>  $url->description_meta]);
        $this->view->registerLinkTag(['rel' => 'canonical',
            'href' =>$url->getRelCononical([$url->controller . '/'. $url->action,'id'=>$url->identity],true) ]);
        if ($url->keywords) $this->view->registerMetaTag([ 'name' => 'keywords', 'content' =>  $url->keywords]);




        $this->view->registerMetaTag([ 'property' => 'og:type', 'content' =>  'article']);
        if ($url->description_meta) $this->view->registerMetaTag([ 'property' => 'og:description',
            'content' =>  $url->description_meta]);
        $this->view->registerMetaTag([ 'property' => 'og:title', 'content' =>  $url->title ]);
        $this->view->registerMetaTag([ 'property' => 'og:url', 'content' => \Yii::$app->request->hostInfo .
            \yii\helpers\Url::to(['/news/news/view','id'=>$blog->id]) ]);
        $imgMain =  Img::getImgMain(237, ImgLinks::T_Ns,$blog->id );
        if ( is_object($imgMain) && is_object($imgMain->img_r)) {
            $this->view->registerMetaTag(['property' => 'og:image',
                'content' => \Yii::$app->request->hostInfo . $imgMain->img_r->src]);
        }
        //$this->view->registerMetaTag([ 'property' => 'og:author', 'content' => 'Koptilka.com' ]);
        $this->view->registerMetaTag([ 'property' => 'og:content_tier', 'content' => 'free' ]);


    }

}
