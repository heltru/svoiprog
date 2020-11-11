<?php

namespace app\modules\textpage\controllers\frontend;


use app\modules\news\models\NewsCat;
use app\modules\textpage\models\Textpage;
use app\modules\url\models\Url;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class DefaultController extends Controller
{


    public function beforeAction($action)
    {
        if ($action->id == 'main') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }


    public function actionMain($id, $url = null)
    {
        $this->layout = 'main';

        if ($url == null) {
            $url = Url::find()->
            where(['identity' => $id, 'controller' => 'textpage', 'action' => 'main'])->one();
        }

        $textpage = $this->findModel($id);

        Url::setHeaderLastMod(strtotime($url->last_mod));

        $this->view->params['cats_header'] = \app\modules\news\service\NewsCat::items_cats_header();

        // $this->view->registerJsFile('/js/main.js', ['position' => \yii\web\View::POS_END]);
        $this->view->params['curr_page'] = 'main';


        $this->addOpenGraphMain($url);

        return $this->render('/textpage/main/main', [
            'url' => $url,
            'textpage' => $textpage,
        ]);

    }


    protected function findModel($id)
    {
        if (($model = Textpage::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    private function addOpenGraphMain($url)
    {

        $this->view->title = $url->title;
        if ($url->description_meta) $this->view->registerMetaTag([
            'name' => 'description',
            'content' => $url->description_meta,
        ]);
        $this->view->registerLinkTag(['rel' => 'canonical', 'href' =>
            Yii::$app->request->hostInfo
        ]);
        if ($url->keywords) $this->view->registerMetaTag(['name' => 'keywords', 'content' => $url->keywords]);

        $this->view->registerMetaTag(['property' => 'og:type', 'content' => 'website']);

        $this->view->registerMetaTag(['property' => 'og:site_name', 'content' => Yii::$app->params['domain']]);
        $this->view->registerMetaTag(['property' => 'og:url', 'content' => 'https://' . Yii::$app->params['domain']]);
        $this->view->registerMetaTag(['property' => 'og:locale', 'content' => 'ru_RU']);
        $this->view->registerMetaTag(['property' => 'og:image', 'content' =>
            Yii::$app->request->hostInfo . '/images/theme/logo-top.png']);

        $this->view->registerMetaTag(['property' => 'og:title', 'content' => $url->title]);
        if ($url->description_meta)
            $this->view->registerMetaTag(['property' => 'og:description', 'content' => $url->description_meta]);
    }


}
