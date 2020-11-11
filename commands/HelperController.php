<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;


use app\modules\alco\models\Cocktail;
use yii\console\Controller;
use yii\helpers\BaseFileHelper;
use yii\helpers\Console;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class HelperController extends Controller
{

    public function actionTimeup(){

        foreach ( Cocktail::find()->all() as $model ){
            $model->date_crt = strtotime($model->date_cr);
            $model->update(false,['date_crt']);
        }

    }



    public function actionGendata(){
        $timestart =  strtotime('2019-01');

     //   exit;
        for($i = 1 ;$i<=365*2;$i++){
            $date_plus_day = strtotime("+$i day",$timestart);

            for($j = 1 ;$j<= 24;$j++){

                $cocktail = new Cocktail();
                $cocktail->device_id = 1;
                $cocktail->cola = rand(45,140);
                $cocktail->ice = rand(45,80);
                $cocktail->wisky = rand(45,90);


                $datetime = strtotime("+$j hour",$date_plus_day);

                $cocktail->date_cr = date('Y-m-d H:i:s',$datetime);
                $cocktail->date_crt = $datetime;

                $cocktail->price = 150;
                $cocktail->save();

            }



        }
    }


    public function actionDomainApi(){

        $site_domain_id = 2241;
        $site_domain = GDomainSite::findOne(['id'=>$site_domain_id]);

        $app = new NDomainApi($site_domain->gSite_r,$site_domain->gDomain_r);
        $app->begin();


    }


    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex($message = 'hello world')
    {
        echo $message . "\n";
    }



    private function log($msg,$success=true)
    {
        if ($success) {
            $this->stdout($msg, Console::FG_GREEN, Console::BOLD);
        } else {
            $this->stderr($msg, Console::FG_RED, Console::BOLD);
        }
        $this->stdout(PHP_EOL);
    }

}
