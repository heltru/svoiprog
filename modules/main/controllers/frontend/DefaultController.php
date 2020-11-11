<?php

namespace app\modules\main\controllers\frontend;

use app\modules\helper\models\Helper;
use app\modules\show\models\ShowRegister;
use app\modules\show\models\TrackAuto;
use app\modules\show\models\TrackPoint;
use app\modules\zapros\models\Subscr;
use app\modules\zapros\models\Zapros;
use yii\helpers\Json;
use yii\web\Controller;

class DefaultController extends Controller
{
 public $layout = '/adminlte/main-login';

   public function actions()
    {
        /*
        return [
             'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
        */
    }

    public $enableCsrfValidation = false;

    public function actionIndex()
    {
        $title = \Yii::$app->name;
        $this->view->title =$title;

        $this->layout = 'landing';
        return $this->render('landing/main');
    }


    public function actionError()
    {

        return $this->render('error');

    }


}
