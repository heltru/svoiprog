<?php
/**
 * Created by PhpStorm.
 * User: o.trushkov
 * Date: 18.12.17
 * Time: 11:50
 */

namespace app\modules\url\services;



use app\modules\url\components\Transliteration;
use app\modules\url\models\Url;
use Yii;
use app\modules\url\models\UrlDomain;
use yii\helpers\ArrayHelper;

class UrlService
{


    public $selected_domains;


    public function changePublic(Url $url ,$on = true){
        $url->public =  ($on) ?  Url::P_OK  : Url::P_NO;
    }

    public function getUrl(){

    }

    public function addMainPage($form,$conf = ['controller'=>'textpage','identity'=>null,'action'=>'view']){

        $url = new Url();
        $url->setScenario('validMainPage');
        $url->href = $form->href;
        $url->real_canonical = $form->real_canonical;
        $url->title = $form->title;
        $url->h1 = $form->h1;
        $url->description_meta = $form->description_meta;
        $url->redirect = $form->redirect;
        $url->controller = $conf['controller'];
        $url->crs = $form->crs;
        $url->domain_id = $form->domain_id;
        $url->action = $conf['action'];
        $url->pagination = $form->pagination;
        $url->identity = $conf['identity'];
        $url->keywords = $form->keywords;


    }

    public function add($url,$conf = ['controller'=>'textpage','identity'=>null,'action'=>'view']){

        $url->setScenario('validMainPage');

        $url->controller = $conf['controller'];
        $url->action = $conf['action'];
        $url->identity = $conf['identity'];
        if ($url->save()){
            $this->preseachUrlDomain($url);
        }

        return $url;

    }

    private function preseachUrlDomain($model){

        $selected_domains = Yii::$app->request->post('selected_domains');
        if ( $selected_domains === null){
            $selected_domains = $this->selected_domains;
        }

        if ( is_string($selected_domains) && strlen($selected_domains)) {
            $arrSets = explode(',', $selected_domains);

            $old_links = UrlDomain::findAll(['url_id'=>  $model->id]);
            $old_domains = ArrayHelper::getColumn($old_links,'domain_id');

            $del_domain_id = array_diff($old_domains,$arrSets);

            foreach ($old_links as $link){
                if ( in_array( $link->domain_id,$del_domain_id)){
                    $link->delete();
                }
            }

            $new_domain_ids = array_diff($arrSets,$old_domains);

            foreach ( $new_domain_ids as $new_domain_id ){
                $l = new UrlDomain();
                $l->url_id = $model->id;
                $l->domain_id = (int) $new_domain_id;
                $l->save();
            }


        }  else {
            UrlDomain::deleteAll(['url_id'=>  $model->id]);
        }
    }


    public function findUrl($conf=['controller'=>'textpage','identity'=>null,'action'=>'view','redirect'=>0]) //id action
    {

        if (    (   $prUrl = Url::findOne($conf) ) !== null) {

            if ($prUrl->redirect == 0){
                return  $prUrl;
            }
            $prUrl = Url::checkRedirect($prUrl);
            return  $prUrl;
        } else {
            return new Url();
        }


        //throw new NotFoundHttpException('The url by product page does not exist.');
    }



    public  function transliterate($txt){


        $smth = preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode( $txt));
        $txt =  html_entity_decode( $smth,null,'UTF-8');
        $txt = mb_strtolower ($txt);


        $replacement = '-';
        $translator = new Transliteration();
        $translator->standard = Transliteration::GOST_779B;

        $txt = $translator->transliterate($txt);

        $string = preg_replace('/[^a-zA-Z0-9=\s—–-]+/u', '', $txt);
        $string = preg_replace('/[=\s—–-]+/u', $replacement, $string);
        $txt = trim($string, $replacement);
        return $txt;
    }




}