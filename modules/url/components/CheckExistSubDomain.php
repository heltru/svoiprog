<?php
namespace app\modules\url\components;

use yii\web\ForbiddenHttpException;
use yii\web\UrlRuleInterface;
use Yii;

class CheckExistSubDomain  implements UrlRuleInterface
{



    public function createUrl($manager, $route, $params)
    {

        return false;

    }


    public function parseRequest($manager, $request)
    {

        $need403 = false;


        $domain = Yii::$app->getModule('domain');
        $d = $domain->getDomainId();


        // 3 - domain.koptilka.com ; 4 - domain.testeav.it-06.aim

        $hn = \Yii::$app->request->hostName;
        $domain_name = explode('.',$hn);




        if ( YII_ENV_DEV ) { //test env
            if (count($domain_name) == 4 && $d === null){ //subdomain
                $need403 = true;
            }
        } else {

            if (count($domain_name) == 3 && $d === null){ //subdomain
                $need403 = true;
            }

        }

        if ($need403){
            throw new ForbiddenHttpException('You are not allowed to access this page.');
        }



        return false;
    }



}

