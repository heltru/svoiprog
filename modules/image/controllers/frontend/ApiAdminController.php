<?php

namespace app\modules\image\controllers\frontend;

use app\modules\basket\models\Basket;

use app\modules\blockdescr\models\BlockDescr;
use app\modules\catalog\models\frontend\Cat;

use app\modules\comment\models\Comments;
use app\modules\compare\models\CpEntity;
use app\modules\download\models\ProductDownload;
use app\modules\eav\models\EavA;
use app\modules\image\models\Img;
use app\modules\image\models\ImgLinks;
use app\modules\image\services\ApiImage;
use app\modules\product\models\ProductModification;
use app\modules\product\models\ProductRecomendSet;
use app\modules\product\models\ProductRelevantSet;
use app\modules\product\service\ProductManager;
use app\modules\url\services\UrlService;
use app\modules\varentity\models\Varentity;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use Yii;
use app\modules\product\models\frontend\Product;
use yii\web\NotFoundHttpException;

use app\modules\url\models\Url;
use yii\web\Response;

/**
 * Default controller for the `textpage` module
 */
class ApiAdminController extends Controller
{



    public $enableCsrfValidation = false;

    public function actionImage(){
        $api_image = new ApiImage();
        Yii::$app->response->format = Response::FORMAT_JSON;

        $imgs = $api_image->getImages(Yii::$app->request->get());

        return ['result'=>'success','data'=>$imgs];


    }



}
