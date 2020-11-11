<?php
namespace app\modules\url\components;

//use common\models\Domain;
//use common\models\GeoCity;
use app\modules\url\models\Url;
use app\modules\url\models\UrlRedirect;
use yii\db\Expression;
use yii\web\UrlRuleInterface;
//use common\models\Product;
//use common\models\Url;
/**
 * Created by PhpStorm.
 * User: o.trushkov
 * Date: 24.05.17
 * Time: 13:45
 *
 */

class AutoRedirectRule  implements UrlRuleInterface
{



    public function createUrl($manager, $route, $params)
    {
        // TODO: Implement createUrl() method.
        return false;

    }


    public function parseRequest($manager, $request)
    {


        $pathInfo = $request->getPathInfo(); //koptilna-kasseler/2

        $red = UrlRedirect::find()->where(['url_in'=>$pathInfo])->one();


        if ($red !== null){
            $furl = $red->url_out;
            if (  strpos($red->url_out,'http') === false){
                $furl = \Yii::$app->request->hostInfo . '/' . $red->url_out;
            }
        }



        if ($red !== null){
            return ['url/url-redirect/make-redirect',['url'=>$furl,'record'=>$red]];
        }

        $pathInfo = \Yii::$app->request->hostInfo . '/' . $pathInfo;
        //ex($pathInfo);

        $red = UrlRedirect::find()->where(['url_in'=>$pathInfo])->one();



        if ($red !== null){
            return ['url/url-redirect/make-redirect',['url'=>$red->url_out,'record'=>$red]];
        }

        return false;
    }



}

