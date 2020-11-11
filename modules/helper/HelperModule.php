<?php

namespace app\modules\helper;

/**
 * helper module definition class
 */
class HelperModule extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    const F_DT = "Y-m-d H:i:s";
    public $controllerNamespace = 'app\modules\helper\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }

    public static function convertDateToDatetime($datetime=null){
        if (! $datetime){
            $mysqldate  = date( 'Y-m-d H:i:s' );

        } else {
            $phpdate = strtotime($datetime );
            $mysqldate = date( 'Y-m-d H:i:s', $phpdate );
        }
        //12-04-2017
        //YYYY-MM-DD HH:MM:SS
       // $phpdate = strtotime($datetime );

        return $mysqldate;
    }

    public static function formatPrice($price){
        return number_format((float)$price , 2, '.', '');
    }

    public static function checkIsset($key,$arr){
        return (boolean) (isset( $arr[$key]) );
    }
    public static function getAVal($key,$arr){
        return   (isset( $arr[$key]) ) ?  $arr[$key] : null;
    }

    public static function formatPhoneDB($phone){

        $phone = str_replace([' ','(',')','-'],['','','',''], $phone);
        return $phone;
    }

}
