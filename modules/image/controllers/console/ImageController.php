<?php

namespace app\modules\image\controllers\console;


use app\modules\image\models\ImgLinks;
use app\modules\image\models\TinypngKeys;
use app\modules\image\services\AttImg;
use app\modules\image\services\CreateWebP;
use app\modules\image\services\Remove2WebP;
use yii\console\Controller;
use yii\console\Exception;
use yii\helpers\Console;
use app\modules\image\models\Img;
use Yii;
/**
 * Interactive console image manager
 */
class ImageController extends Controller
{



    //php yii  image/image/image_create_webp
    // /opt/php72/bin/php yii image/image/image_create_webp

    public function actionImage_create_webp(){
        $app = new CreateWebP();
        $app->begin();
    }



    //php yii  image/image/image_remove_2webp
    // /opt/php72/bin/php yii image/image/image_remove_2webp

    public function actionImage_remove_2webp(){
        $app = new Remove2WebP();
        $app->begin();
    }



    //php yii  image/image/image_remove_empty_img
    // /opt/php72/bin/php yii image/image/image_remove_empty_img

    public function actionImage_remove_empty_img(){
       foreach (Img::find()->where(['webp'=>1])->all() as $img) {


           if ( ! file_exists('web/' . $img->name_image)) {
               $this->log($img->id . ' ' . $img->name_image);

               $img->delete();

               $img_link = ImgLinks::find()->where(['id'=>$img->id])->one();
               if ($img_link !== null){
                   $img_link->delete();
               }


           }

       }
    }



    //php yii image/image/optim_all
    //  /opt/php72/bin/php yii image/image/optim_all
    public function actionOptim_all(){
        $allImg = Img::find()
            ->innerJoin('img_links','img_links.id_image = img.id')
            ->andWhere(['!=','img_links.type','offer_yml'])
            ->andWhere(['fullsize'=>0,'original'=>0])
            ->andWhere(['!=','width',0])
            ->andWhere(['!=','height',0])
            ->andWhere(['!=','optimize',1])


            ->all();



        foreach ( $allImg as $img){
            $file_path =  Yii::$app->params['path_env'] . 'web/'. $img->name_image;
            if (! file_exists($file_path)){
                $this->log('file no exs ' , $file_path);
                continue;
            }

            $res = $this->optimizeImg( $file_path);
            if ($res !== null){
                $img->optimize = 1;
                $img->update(false,['optimize']);
            }
            //Yii::$app->end();
        }
    }


    private function optimizeImg($src,$to=null){
        if ($to === null) $to = $src;
        if (! file_exists($src) || ! file_exists($to)) return null;



        $rec_key = TinypngKeys::find()->where(['<','count_uses',500])->one();

        if ($rec_key === null){
            return null;
        }


        $tinyPngKey = $rec_key->key;



        try {
            \Tinify\setKey($tinyPngKey);
            \Tinify\validate();
        } catch(\Tinify\Exception $e) {
            ex($e->getMessage());
            // Validation of API key failed.
            return null;
        }

        try {
            // Use the Tinify API client.
            $source = \Tinify\fromFile($src);

            $oldSize = @filesize($src);

            $sizeNew =  $source->toFile($to);





            if ( $sizeNew ){

                $log  = 'compress ' . round( ( ((100*$sizeNew)/$oldSize ) ),2) . '% '
                     .round( $oldSize / 1024) .' кБ - ' . round( $sizeNew / 1024) .' кБ ';
                $this->log($log);

            }


            //update count uses
            $rec_key->count_uses = $rec_key->count_uses + 1;

            $rec_key->update(false,['count_uses']);


            return $sizeNew;

        }  catch(\Exception $e) {
            ex($e->getMessage());
            return null;
            // Something else went wrong, unrelated to the Tinify API.
        }

        return null;
    }


    //php yii image/image/optimize
    public function actionOptimize()
    {

        $this->checkCount();
        $attimg = new AttImg();

        $imgs = Img::find()->where(['optimize'=>0,'original'=>0])->all();

        foreach ($imgs as $img) {
            /** @var Img $img */
            $this->stdout($img->name_image);
            $this->stdout(PHP_EOL);

            /*$size = $attimg->optimizeImg($img->name_image);


            if ((int)$size) {
                $this->stdout(' OK', Console::FG_GREEN, Console::BOLD);
            } else {

                $this->stderr(' FAIL', Console::FG_RED, Console::BOLD);
            }
            $this->stdout(PHP_EOL);*/

        }

        $this->stdout('Done!', Console::FG_GREEN, Console::BOLD);
        $this->stdout(PHP_EOL);
    }


    private function checkCount(){

        $settings = \Yii::$app->getModule('settings');
        $count =  (int)$settings->getVar('tinypng_count');

        if ($count >= 500){

            $this->stderr(' FAIL', Console::FG_RED, Console::BOLD);
            $this->stdout(' Лимит api ключа исчерпан', Console::FG_GREY);
            $this->stdout(PHP_EOL);

            Yii::$app->end();
        }


    }



    private function readValue($user, $attribute)
    {
        $user->$attribute = $this->prompt(mb_convert_case($attribute, MB_CASE_TITLE, 'utf-8') . ':', [
            'validator' => function ($input, &$error) use ($user, $attribute) {
                $user->$attribute = $input;
                if ($user->validate([$attribute])) {
                    return true;
                } else {
                    $error = implode(',', $user->getErrors($attribute));
                    return false;
                }
            },
        ]);
    }

    /**
     * @param bool $success
     */
    private function log($str,$color = Console::FG_GREEN,$font_w = Console::FG_GREEN)
    {
        $this->stdout($str,$color,$font_w);
        $this->stdout(PHP_EOL);
    }

}