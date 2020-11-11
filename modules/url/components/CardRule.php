<?php
namespace app\modules\url\components;


use app\modules\url\models\Url;
use app\modules\url\models\UrlDomain;
use yii\db\Expression;
use yii\web\UrlRuleInterface;

use Yii;
/**
 * Created by PhpStorm.
 * User: o.trushkov
 * Date: 24.05.17
 * Time: 13:45
 *
 */

class CardRule  implements UrlRuleInterface
{

    private  $deph=0;
    private $url_vars;

    private $isGeo;


    public function createUrl($manager, $route, $params)
    {

        $this->deph = 0;


        $route_parts = explode('/',$route); //check count == 2



//        if ($route == 'catalog/view'){
//
//            ex($route);
//        }

        if (isset($params['id']) && count($route_parts) == 3) {


//            if ($route_parts[1] == 'default'){
//                array_splice($route_parts, 1, 1);
//            }

//            if ($route == 'news/cat/view'){
//                            ex([
//                                'identity'=>$params['id'],
//                                'module'=>$route_parts[0],
//                                'controller'=>$route_parts[1],
//                                'action'=>$route_parts[2]
//                            ]);
//            }


            $urlM = Url::find()->where([
                'identity'=>$params['id'],
                'module'=>$route_parts[0],
                'controller'=>$route_parts[1],
                'action'=>$route_parts[2]
            ]);

//            if ( ! \Yii::$app->user->can('admin')) {
//
//                $urlM->andWhere(['public' => Url::P_OK]);
//            }

            $urlM = $urlM->one();


             if (  $urlM !== null && $urlM->rawHref ) {


                 $urlM = $this->checkRedirect($urlM);

                 unset($params['id']);

                 $href = $urlM->rawHref;


                 if ( isset($params['url']) ){
                     unset($params['url']);
                 }

                 if ( isset($params['page']) ){
                     $href .= '/' . $params['page'];
                     unset($params['page']);
                 }

                 $_query = http_build_query($params);

                 $url = (!empty($_query)) ?    $href . '?' . $_query  :   $href;

                 return  $url;
             }  return  false;
        }

        return false;  // данное правило не применимо
    }

    public function parseRequest($manager, $request)
    {
        $this->deph = 0;


        $this->saveUtm();

        $pathInfo = $request->getPathInfo(); //koptilna-kasseler/2


        $pathInfo = trim($pathInfo, '/');


        $expPath =  explode('/',$pathInfo);
        $page = null;



        if( count($expPath) > 1){
            $lastParam = array_pop($expPath);
            $a = (int) $lastParam;
            $a.='';

            if ( $lastParam == $a  && $a > 0 /*is_integer(  $lastParam  )*/ ){
                $page = $lastParam;
            }
        }

        $result = $this->findUrl($pathInfo,['page'=>null]);


        if(!empty($result)){
            return $result;
        }





        if( $page !== NULL ){

            $result = $this->findUrl(join('/',$expPath),['page'=>$page],1);

            if( empty($result) ){
                // check suffiks for gfeed
                $result = $this->findUrl(join('/',$expPath),['mod_id'=>$page,'page'=>null]);
            }
            if( empty($result) ){
                return false;
            }
            return $result;
        }





        $result = $this->findUrl($pathInfo,['page'=>null]);

        if(!empty($result)){
            return $result;
        }
        /// dyn

        $pathInfo = $this->urlVarParse($pathInfo);

        $result = $this->findUrl($pathInfo,['page'=>null,'dynamic'=>1]);



        if(!empty($result)){
            return $result;
        }



        return false;  // данное правило не применимо
    }


    //decode dynamic url
    private function urlVarParse($url){ // for REDIRECT
        $all = Url::findAll(['dynamic'=>1]);
        foreach ($all as $item){

            if (strpos( $url , $item->href) !== false){
                $this->url_vars = str_replace($item->href,'',$url);
                return $item->href;
            }
        }
        return $url;
    }


    private function findUrl($urlStr = '',$arg = [ 'page' => null  ]){
        $page = $arg['page'];

        $query = Url::find()->where(['href'=>$urlStr]);


        $query = $this->makeQueryLastMod($query);

       //  ex(  $query->createCommand()->rawSql);
        $url = $query->one();

    //    ex($url);

        if ( ($url !== null) && ( $url->controller ) &&  ( $url->action ) &&  ( $url->identity )){




            if($url->pagination == false && $page !== null){
                return [];
            }


            //dostavka-oblast - geo
            //dostavka-v-adler - geo
            //dostavka - textpage



            if (
                $this->isGeo && (

                    ( $url->controller == 'blog' ||  $url->controller == 'blogcat' )
                    ||
                    ( $url->controller == 'brand' ||  $url->controller == 'brandcat' ||   $url->controller == 'compare' )
                    ||
                    ( ! \Yii::$app->getModule('geo')->isRegionCenter() && $url->controller == 'geo')


                )
            ){
                \Yii::$app->response->redirect( '/'   ,301);
            }



            if ( $url->redirect && is_object($url->redirect_r) ){
                $url = Url::checkRedirect($url);
                if ( isset($arg['dynamic'])){
                   \Yii::$app->response->redirect( '/' .$url->rawHref . $this->url_vars ,301);
                } else {
                    \Yii::$app->response->redirect( '/' . $url->rawHref,301);
                }

            }



            $route = "{$url->module}/{$url->controller}/{$url->action}";

            $params = ['id'=>$url->identity,'url'=>$url ];


            if( $page !== NULL  ){
                $params['page'] = $page;
            }

            if($page == 1){
                $this->redirectPagination($urlStr);
            }

            if (isset($arg['mod_id'])){
                $params['mod_id'] = $arg['mod_id'];
            }



            return [ $route,$params];
        }

        return [];
    }


    private function redirectPagination( $urlStr ){

        $querySrt  = \Yii::$app->request->queryString;
        $redirUrl = '/' .$urlStr;

        if ($querySrt){
            \Yii::$app->response->redirect( $redirUrl . '?' . urldecode($querySrt), 301);
        } else {
            \Yii::$app->response->redirect( $redirUrl  , 301);
        }

    }


    private function saveUtm(){

        $session = \Yii::$app->session;
        $session->open();

        $url = \Yii::$app->request->url;

        if ( ! $session->get('first_url') ){
                $session->set('first_url',$url);
        }

        if (Yii::$app->request->get('main') !== null){
            $session->set('main',1);
        }

        $session->set('last_url',$url);

        if (Yii::$app->request->get('notmain') !== null){
            $session->remove('main');
        } else {
            $session->set('main',1);
        }



        $session->close();

    }

    private function checkRedirect($urlM){
        $this->deph ++;
        if($urlM->redirect == 0 || $this->deph > 5){
            return $urlM;
        }
        $original = clone $urlM;
        $urlM = Url::find()->where(['id'=>$urlM->redirect])->one();

        if ($urlM !== null ){
            return $this->checkRedirect($urlM);
        }
        return $original;
    }



    private function makeQueryLastMod($query){

        if ( ! \Yii::$app->user->can('admin')) {

            $time = new \DateTime('now');
            $today = $time->format('Y-m-d');

            $query->andWhere(['public'=> Url::P_OK]);

            $query->andWhere([ '<=',   'url.last_mod', $today . ' 00:00:00']);
            //$query->andWhere([ '<=',   new Expression('DATE(url.last_mod)'), $today]);
        }
        return $query;
    }

}

