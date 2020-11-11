<?php

namespace app\modules\test\controllers\frontend;

use app\modules\alco\models\Cocktail;
use app\modules\car\models\Car;
use app\modules\helper\models\Helper;
use app\modules\test\app\SiteError;
use app\modules\test\models\CityTransport;
use app\modules\test\models\CityTransportCheck;
use app\modules\test\models\CityTransportStat;
use app\modules\user\models\User;
use yii\helpers\Json;
use yii\web\Controller;
use Yii;
use yii\base\Model;

use DateTime;
use DateInterval;
use DatePeriod;
/**
 * Default controller for the `test` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */

    public $email = 'laneo2007@yandex.ru';

    public function actionClear(){

        $k = 0;
        foreach ( Cocktail::find()->orderBy(['id'=>SORT_DESC])->all() as $model ){
            if ($k == 17520){
                ex('nice');
            }
            $model->delete();
            $k ++;
        }

    }

    public function actionTimeup(){

        foreach ( Cocktail::find()->all() as $model ){
            $model->date_crt = strtotime($model->date_cr);
            $model->update(false,['date_crt']);
        }

    }

    public function actionDate(){
        $datetime_tone = date('Y-m-d H:i:s', strtotime('-1 month', strtotime('+1 day')));
        $datetime_ttwo = date('Y-m-d H:i:s');

        $datetime_tone = strtotime($datetime_tone); //1
        $datetime_ttwo = strtotime($datetime_ttwo); //2

        $char_labels = [];


        $d = 60*60*24*7;
        while ($datetime_ttwo > $datetime_tone){

            $char_labels[] = date('Y-m-d W',$datetime_ttwo);
            $datetime_ttwo = $datetime_ttwo - $d;
        }



        /*
        $datetime_tone = date('Y-m-d H:i:s', strtotime('-1 month', strtotime('+1 day')));
        $datetime_ttwo = date('Y-m-d H:i:s');

        $begin = new DateTime($datetime_tone);
        $end = new DateTime($datetime_ttwo);
      //  $diff = $end->diff($begin);
      //  $end->modify('-1 month');

        $interval = DateInterval::createFromDateString('1 week');
        $interval->invert = 1;
        //$period = new DatePeriod($begin, $interval, $end);
        $period = new DatePeriod($begin, $interval,$end);

        $char_labels = [];

        foreach ($period as $dt) {

            $char_labels[] = $dt->format('d.m.Y W');


        }

        ex($char_labels);
*/
        $startDate = new DateTime('-1 month');
        $endDate = new DateTime();
        $period = new DatePeriod($startDate, new \DateInterval('P7D'), $endDate->modify('+1 day'));

        foreach ($period as $date) {
            $char_labels[] = $date->format('d-m-y');

        }

        ex($char_labels);

    }

    public function actionGendata(){
        for($i = 1 ;$i<=366*2;$i++){
            for($j = 0 ;$j<= 24;$j++){

                $cocktail = new Cocktail();
                $cocktail->device_id = 1;
                $cocktail->cola = rand(45,140);
                $cocktail->ice = rand(45,80);
                $cocktail->wisky = rand(45,80);
                $date = strtotime("+$i day");
                $datetime = strtotime("+$j hour",$date);
                $cocktail->date_cr = date('Y-m-d H:i:s',$datetime);

                $cocktail->price = 150;
                $cocktail->save();

            }

        }
    }

    public function actionDataTest(){
        /*
        $all = Cocktail::find()->limit(1)->all();
        $x = $all[0]->cola;
        $x = $x-floor($x);
        $x = $x == 0;
        $y = $all[0]->ice;
        $y = $y-floor($y);
        $y = $y == 0;
        $z = $all[0]->wisky;
        $z = $z-floor($z);
        $z = $z == 0;

        $b =   ! ($x && $y && $z);

        ex([
           $b
        ]);

*/
        $all = Cocktail::find()->all();
        foreach ($all as $item){
            $x = $item->cola;
            $x = $x-floor($x);
            $x = $x == 0;
            $y = $item->ice;
            $y = $y-floor($y);
            $y = $y == 0;
            $z = $item->wisky;
            $z = $z-floor($z);
            $z = $z == 0;

            $b =  !($x && $y && $z);

            if ($b){
                $item->test = 0;
                $item->update(false,['test']);
            }
        }
    }

    public function actionVk(){
        $this->layout = false;
        return $this->render('vk');
    }

    public function actionTestEmail(){

        Yii::$app->mailer->compose()
            ->setFrom('info@mirovid.ru')
            ->setTo('mirovidweb@yandex.ru')
            ->setSubject('test')
            ->setTextBody('test')
            ->setHtmlBody('test a')
            ->send();


    }

    private function checkCarTimeLimit($car,$area){
        $date = Helper::mysql_datetime(strtotime("+25 minutes"));
        $old =  CityTransportCheck::find()->where(['>','date',$date])
            ->andWhere(['gn'=>$car['gn']])
            ->andWhere(['area'=>$area])
            ->andWhere(['number'=>$car['number']])
            ->one();

        return $old === null;

    }

    public function actionTestTimeF(){
        return $this->render('t');
    }


    public function actionTestTime()
    {
        set_time_limit(0);

        $sqrs  = [ [ [58.587693, 49.621885],[58.593872, 49.636380] ] , [ [58.621161, 49.638791],[58.627197, 49.651354] ] ];

        $routes = [1090,1054,1033,1037,1017,1051,1046,1053,1074,1061,1001,1023,1022,1010,1016,1044,1002,1070,1012,1039,1088,1014,1087,1021,1084,5007,5005,5008,5014,5001,5004,5003];


        $t = [];

        foreach ($routes as $route){
            $d = file_get_contents('https://cdsvyatka.com/api/kirov/map/route/'.$route.'/transport');

            $d = Json::decode($d);
            $t[] = $d;
            foreach ($d as $car) {

                foreach ($sqrs as $num_area => $sqr){

                    $lat = $car['lat'] >= $sqr[0][0] && $car['lat'] <= $sqr[1][0];
                    $long = $car['lng'] >= $sqr[0][1] && $car['lng'] <= $sqr[1][1];

                    if ($lat && $long && $this->checkCarTimeLimit($car,$num_area)){
                        $rec = new CityTransportCheck();
                        $rec->long = $car['lng'];
                        $rec->lat = $car['lat'];
                        $rec->gn = $car['gn'];
                        $rec->number = $car['number'];
                        $rec->date = $car['date'];
                        $rec->route_id = $route;
                        $rec->area = $num_area;
                        $rec->save();
                        if ($rec->getErrors()){
                            ex($rec->getErrors());
                        }

                    }
                }


            }
            sleep(rand(1,3));
        }
//ex($t);


    }

    public function actionTestTime1(){
        set_time_limit(0);

        $sqrs  = [   [ [58.621161, 49.638791],[58.627197, 49.651354] ] ];

        $routes = [1090,1054,1033,1037,1017,1051,1046,1053,1074,1061,1001,1023,1022,1010,1016,1044,1002,1070,1012,1039,1088,1014,1087,1021,1084,5007,5005,5008,5014,5001,5004,5003];



        foreach ($routes as $route){
            $d = file_get_contents('https://cdsvyatka.com/api/kirov/map/route/'.$route.'/transport');

            $d = Json::decode($d);
            foreach ($d as $car) {

                foreach ($sqrs as $num_area => $sqr){

                    $lat = $car['lat'] >= $sqr[0][0] && $car['lat'] <= $sqr[1][0];
                    $long = $car['lng'] >= $sqr[0][1] && $car['lng'] <= $sqr[1][1];

                    if ($lat && $long && $this->checkCarTimeLimit($car,1)){
                        $rec = new CityTransportCheck();
                        $rec->long = $car['lng'];
                        $rec->lat = $car['lat'];
                        $rec->gn = $car['gn'];
                        $rec->number = $car['number'];
                        $rec->date = $car['date'];
                        $rec->route_id = $route;
                        $rec->area = 1;
                        $rec->save();
                        if ($rec->getErrors()){
                            ex($rec->getErrors());
                        }

                    }
                }


            }
            sleep(rand(1,3));
        }

    }

    public function actionTest(){

        $this->layout = false;
        return $this->render('a');

        /*
        Yii::$app->mailer->compose(['text' => '@app/modules/user/mails/test'])
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
            ->setTo($this->email)
            ->setSubject('Email confirmation for ' . Yii::$app->name)
            ->send();
        mail('test@test.com','Email confirmation for ' . Yii::$app->name,'test');
        echo '123';
        */
        /*
        Yii::$app->mailer->compose()
            ->setFrom('89991002878@mail.ru')
            // ->setTo('757537s@mail.ru')
            ->setTo('laneo2007@yandex.ru')
            ->setSubject('Заказ звонка с сайта novaferm.ru')
            //->setTextBody('Ваша заявка №'.$model->id.' принята. В течении недели мы свяжимся с вами, по телефону или по почте.')
            ->setHtmlBody(' ФИО ')
            ->send();
        */
        /*
        $user = new User();
        $user->username ='123';
        $user->email = 'test@test.test';
        $user->setPassword(3423);
        $user->status = User::STATUS_WAIT;

        mail($this->email,
            'Email confirmation for ' . Yii::$app->name,
            Yii::$app->getView()->renderFile('@app/modules/user/mails/emailConfirm.php',['user' => $user])
        );*/
    }




}
