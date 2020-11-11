<?php

namespace app\modules\url;
use app\modules\product\models\Product;
use app\modules\url\models\Url;
use app\modules\url\models\UrlDomain;
use Yii;
/**
 * url module definition class
 */
class UrlModule extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
   // public $controllerNamespace = 'app\modules\url\controllers';

    /**
     * @inheritdoc
     */

    public $url;


   public function init()
   {
       parent::init(); // TODO: Change the autogenerated stub
       $this->find_url();
   }

    private function find_url(){


        if ($this->url === null){
            $this->url = Url::findOne(['href'=>Yii::$app->request->getPathInfo()]);

        }
    }

    public static function t($category, $message, $params = [], $language = null)
    {
        return Yii::t('modules/admin/' . $category, $message, $params, $language);
    }

    public function lowerLvlCatUrlForProducts(){

        ////last_update_service_add_red_by_cat

        $settings = \Yii::$app->getModule('settings');
        $lm = (int)$settings->getVar( 'last_update_service_add_red_by_cat') + (60*60*24);

        if ( time() > $lm ) {
            $settings->editVar('last_update_service_add_red_by_cat', time());
        } else  {
            return;
        }



        $products =  Product::find()->
            leftJoin('cat','cat.id = product.cat_id')->
            andWhere(['cat.parent_id'=>[15,16]])->
        //select('product.id,product.status,product.name,product.cat_id')->
        joinWith(['url_rr'=>function($q){$q->domain();}],true,'INNER JOIN')->
        andWhere(['product.status'=> Product::ST_OK ])->
        all();


        $list = [];
        $limit = 4;
        $cn = 0;
        foreach ($products  as $product) {



            $url = $product->url_rr;

            if (is_object($url)){

                $c = explode('/',$url->rawHref );
                $list[count($c)][] = $url->rawHref ;

                if ( $cn >= $limit ) {
                    //echo 'complete';
                    //continue;
                    break;
                }

                if (count($c) == 3){

                    $new = $c[1] . '/' . $c[2];

                    $r = Yii::$app->getModule('url')->createRedirect($url->rawHref,$new);

                    if (! $r) {
                        //echo 'ERROR';
                        return;
                    }
                    else {
                        $cn ++;
                    }




                }

            }


        }

    }


    public function createRedirect($oldHref,$newHref){

        $valid =  $this->validRedirectFields($oldHref,$newHref);

        $modelOld = Url::find()->where(['href'=>$oldHref])->one();

        if ($valid && $modelOld !== null){


            $modelNew = clone $modelOld;
            $modelNew->isNewRecord = true;
            $modelNew->attributes = $modelOld->attributes;
            $modelNew->id = null;
            $modelNew->href = $newHref;
            $modelNew->setScenario('validHref');


            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            try {

                if ($modelNew->save()){

                    $modelOld->redirect = $modelNew->id;
                    if ($modelOld->update(false,['redirect'])){



                        foreach ( $modelOld->urlDomain_r as $link){
                            $l = new UrlDomain();
                            $l->url_id = $modelNew->id;
                            $l->domain_id = $link->domain_id;
                            $l->save();
                        }

                        $transaction->commit();
                        return $modelNew;
                        //return true;


                    } else {
                        foreach ($modelOld->getErrors() as $attr => $error){
                            Yii::$app->session->setFlash('danger', $error[0]);
                        }
                    }

                } else {
                    foreach ($modelNew->getErrors() as $attr => $error){
                        Yii::$app->session->setFlash('danger', $error[0]);
                    }
                }

            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            } catch (\Throwable $e) {
                $transaction->rollBack();
                throw $e;
            }

        }
        return false;
    }


    private function validRedirectFields($old,$new){
        $valid = false;
        if ( ! ($old && $new)){
            return $valid;
        }
        if ($old === $new){
            return $valid;
        }


        $modelOld = Url::find()->where(['href'=>$old])->one();
        if ($modelOld === null){
            Yii::$app->session->setFlash('danger',
                'Старый url не найден');
            return $valid;
        }

        $modelNew = Url::find()->where(['href'=>$new])->one();

        if ($modelNew !== null){
            Yii::$app->session->setFlash('danger',
                'Новый url уже есть, воспользуйтесь операцией копирования url, если это требуется');
            return $valid;
        }

        return true;

    }


}
