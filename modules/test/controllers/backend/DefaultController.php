<?php

namespace app\modules\test\controllers\backend;

use app\modules\app\app\AddNewUser;
use app\modules\app\app\AppCreateMem;
use app\modules\app\app\AppMemDelete;
use app\modules\app\app\AppNovaVidShow;
use app\modules\block\models\Block;
use app\modules\block\models\BlockMsg;
use app\modules\block\models\Msg;
use app\modules\block\models\MsgDaytime;
use app\modules\block\models\MsgLocale;
use app\modules\block\models\MsgLocaleCost;
use app\modules\car\models\Car;
use app\modules\helper\models\Helper;
use app\modules\test\models\Gis2015ParseEmail;
use app\modules\test\models\Gis2015ParseSite;
use app\modules\test\models\Gis2016ParseEmail;
use app\modules\test\models\Gis2016ParseSite;
use app\modules\test\models\Gis201908ParseEmail;
use app\modules\test\models\Gis201908ParseSite;
use app\modules\test\models\GisEmail;
use app\modules\test\models\gisParseEmail;
use app\modules\test\models\gisParseSite;
use app\modules\test\models\GisSite;
use app\modules\test\models\GisSiteEmail;
use app\modules\test\models\ParseEmail;
use app\modules\test\models\ParseSite;
use app\modules\test\models\Ra;
use app\modules\test\app\SiteError;

use app\modules\user\forms\frontend\SignupForm;
use app\modules\user\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\web\Controller;
use Yii;
use yii\base\Model;


/**
 * Default controller for the `test` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */


    public function actionMail(){

        $k = 0;
        $all = [];

        foreach (GisEmail::find()->all() as $item){
            if ($k > 2000 and $k < 4000){
                $all[] = [$item->email];
            }

            $k ++;
        }

        //Helper::download_send_headers('emails_2000_400.csv');
        file_put_contents('emails_2000_4000.csv', Helper::array2csv($all));





    }

    private function download_send_headers($filename) {
        // disable caching
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");

        // force download
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");

        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
    }

    private function array2csv(array &$array)
    {
        if (count($array) == 0) {
            return null;
        }
        ob_start();
        $df = fopen("php://output", 'w');
        fputcsv($df, array_keys(reset($array)));
        foreach ($array as $row) {
            fputcsv($df, $row);
        }
        fclose($df);
        return ob_get_clean();
    }


    public static function export($rows, $coldefs, $boolPrintRows=true, $csvFileName=null, $separator=';')
    {
        $endLine = '\r\n';
        $returnVal = '';

        if($csvFileName != null)
        {
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment; filename=".$csvFileName);
            header("Content-Type: application/octet-stream");
            header("Content-Transfer-Encoding: binary");
        }

        if($boolPrintRows == true){
            $names = '';
            foreach($coldefs as $col=>$config){
                $names .= $col.$separator;
            }
            $names = rtrim($names,$separator);
            if($csvFileName != null){
                echo $names.$endLine;
            }else
                $returnVal .= $names.$endLine;
        }

        foreach($rows as $row){
            $r = '';
            foreach($coldefs as $col=>$config){

                if(isset($row[$col])){

                    $val = $row[$col];

                    foreach($config as $conf)
                        if(!empty($conf))
                            $val = Yii::$app->format->format($val,$conf);

                    $r .= $val.$separator;
                }
            }
            $item = trim(rtrim($r,$separator)).$endLine;
            if($csvFileName != null){
                echo $item;
            }else{
                $returnVal .= $item;
            }
        }
        return $returnVal;
    }

    public function actionGenerateUser(){
        $user_name = Yii::$app->request->get('username');
        $user_pass = Yii::$app->request->get('pass');
        $user_email = Yii::$app->request->get('email');



        $login =  Helper::transliterate($user_name);

        $old_user = User::find()->where(['username'=>$login])->one();
        if ($old_user !== null){
            return 1;
        }



        $app_create_user = new AddNewUser();

        $model = new SignupForm();
        $model->email = $user_email;
        $model->username = $user_name;
        $model->password = $user_pass;


        if ($app_create_user->addNewUser($model)) {
            return 0;
        }


    }




    public function actionGenerateUsers(){

        foreach ( Ra::find()
                      /*->andWhere([  '!=','pass',''])->andWhere([  '!=','login',''])*/->all() as $item ){




            $item->login =  Helper::transliterate($item->login);

            $old_user = User::find()->where(['username'=>$item->login])->one();
            if ($old_user !== null){
                continue;
            }

            $item->pass = Yii::$app->security->generateRandomString(6);
            $item->lp = 1;
            $item->update(false,['login','pass','lp']);


            $app_create_user = new AddNewUser();

            $model = new SignupForm();
            $model->email = $item->email;
            $model->username = $item->login;
            $model->password = $item->pass;


            if ($app_create_user->addNewUser($model)) {
                $item->uc = 1;
                $item->update(false,['uc']);
            }



        }
    }

    public function actionDelTestData(){

        $block = Block::findOne(['status'=>Block::ST_TEST]);

        if ($block === null){
            ex('Empty Block');
        }

        foreach ( Msg::findAll(['block_id'=>$block->id]) as $msg){
            $app = new AppMemDelete();
            $app->delete_mem($msg);
        }
    }

    public function actionTestShowReg(){
      ex(
          file_get_contents('http://mirovid/api/car/register-show?file_name=1_5_output-onlinepngtools.png&lat=1&long=2')
      );
    }

    public function actionGenTestData(){

        $block = Block::findOne(['status'=>Block::ST_TEST]);
        if ($block === null){
            ex('Empty Block');
        }


        $app = AppNovaVidShow::Instance();


        foreach ($app->matrix_locales as $num => $value){

            $num_geo = $num+1;

            $time_id = rand(1,336-(336/2));

            $app_cr = new AppCreateMem();
            $msg = new Msg();

            $msg->block_id = $block->id;
            $msg->type = Msg::T_T;
            $msg->content = 'Geo: ' . $num_geo .  ' TF:' . $time_id;
            $msg->date_cr = Helper::mysql_datetime();
            $msg->date_update = Helper::mysql_datetime();
            $msg->status = Msg::ST_OK;


            if ( !  $app_cr->createMem($msg,$block,$msg->getAttributes()) ){
                ex($msg->getErrors());
            }






            $gl = new MsgLocale();
            $gl->msg_id = $msg->id;
            $gl->locale_id = $num_geo;
            $gl->save();



            $glc = new MsgLocaleCost();
            $glc->msg_id = $msg->id;
            $glc->locale_id = $num_geo;
            $glc->cost = 1;
            $glc->save();








        }





    }

    public function actionGisSiteParse(){
        $all_site = ArrayHelper::getColumn(GisSite::find()->all(),'url');
        foreach ($all_site as $url){

                $d = Helper::curl_get($url,[],[   CURLOPT_FOLLOWLOCATION => true]);
                ex($d);
        }

    }

    public function actionCommonGis(){
        $old_email1 = ArrayHelper::getColumn(Gis2015ParseEmail::find()->all(),'email');
        $old_site1 = ArrayHelper::getColumn(Gis2015ParseSite::find()->all(),'url');

        $old_email2 = ArrayHelper::getColumn(Gis201908ParseEmail::find()->all(),'email');
        $old_site2 = ArrayHelper::getColumn(Gis201908ParseSite::find()->all(),'url');

        $old_email3 = ArrayHelper::getColumn(gisParseEmail::find()->all(),'email');
        $old_site3 = ArrayHelper::getColumn(gisParseSite::find()->all(),'url');

        $all_email = [];
        $all_site = [];
        foreach ($old_email1 as $email){
            if ( ! in_array($email,$all_email)){
                $all_email[] = $email;
            }
        }
        foreach ($old_email2 as $email){
            if ( ! in_array($email,$all_email)){
                $all_email[] = $email;
            }
        }
        foreach ($old_email3 as $email){
            if ( ! in_array($email,$all_email)){
                $all_email[] = $email;
            }
        }
        foreach ($old_site1 as $site){
            if ( ! in_array($site,$all_site)){
                $all_site[] = $site;
            }
        }
        foreach ($old_site2 as $site){
            if ( ! in_array($site,$all_site)){
                $all_site[] = $site;
            }
        }
        foreach ($old_site3 as $site){
            if ( ! in_array($site,$all_site)){
                $all_site[] = $site;
            }
        }

        /*$email_query = [];
        foreach ($all_email as $email){
            $email_query[] = "('".$email."')";
        }*/
        $site_query = [];
        foreach ($all_site as $site){
            $site_query[] = "('".$site."')";
        }

        $sql = 'INSERT INTO gis_site (url) VALUES ' .implode(',',$site_query);
        \Yii::$app->db->createCommand($sql)->execute();



    }

    public function actionLoadEmailSiteCsvGis(){
        set_time_limit(0);

        $old_email = ArrayHelper::getColumn(Gis2015ParseEmail::find()->all(),'email');
        $old_site = ArrayHelper::getColumn(Gis2015ParseSite::find()->all(),'url');

        $newFn = 'gis_kirov_2019_08.csv';


        $lines = file($newFn,FILE_IGNORE_NEW_LINES);


        foreach ($lines as $key => $value)
        {

            $row = explode(';',$value);

            if ( $key < 2) continue;
//ex($row);
            if (count($row) == 25){
                $cat = str_replace('"','',$row[5]);
                $subcat = str_replace('"','',$row[6]);
                $urls = trim(str_replace('"','',$row[11]));
                $emails = trim(str_replace('"','',$row[10]));
            } else {
                continue;
            }

            if (!($cat && $subcat)){
                continue;
            }


            if (!$urls) continue;
            $urls = explode(',',$urls);

            foreach ($urls as $url) {
                if ($url && !in_array($url, $old_site)) {
                    $rec = new Gis201908ParseSite();
                    $rec->url = $url;
                    $rec->cat = $cat;
                    $rec->subcat = $subcat;
                    $rec->save();
                }
            }


            if (!$emails) continue;
            $emails = explode(',',$emails);

            foreach ($emails as $email){
                if (! in_array($email,$old_email)) {
                    $rec = new Gis201908ParseEmail();
                    $rec->email = $email;
                    $rec->cat = $cat;
                    $rec->subcat = $subcat;
                    $rec->save();
                }
            }

        }

    }


    private function actionGis201907(){
        set_time_limit(0);

        $old_email = ArrayHelper::getColumn(Gis2015ParseEmail::find()->all(),'email');
        $old_site = ArrayHelper::getColumn(Gis2015ParseSite::find()->all(),'url');

        $newFn = 'gis_kirov_2016.csv';


        $lines = file($newFn,FILE_IGNORE_NEW_LINES);


        foreach ($lines as $key => $value)
        {

            $row = explode(';',$value);

            if ( $key < 2) continue;

            if (count($row) == 10){
                $cat = str_replace('"','',$row[1]);
                $subcat = str_replace('"','',$row[2]);
                $url = trim(str_replace('"','',$row[9]));
                $emails = trim(str_replace('"','',$row[6]));
            } else {
                continue;
            }

            if (!($cat && $subcat)){
                continue;
            }

            //  $row[0] = trim(str_replace('"','',$row[0]));


            if ( $url && !in_array($url,$old_site)){
                $rec = new Gis2016ParseSite();
                $rec->url = $url;
                $rec->cat = $cat;
                $rec->subcat = $subcat;
                $rec->save();
            }


            if (!$emails) continue;
            $emails = explode(',',$emails);

            foreach ($emails as $email){
                if (! in_array($email,$old_email)) {
                    $rec = new Gis2016ParseEmail();
                    $rec->email = $email;
                    $rec->cat = $cat;
                    $rec->subcat = $subcat;
                    $rec->save();
                }
            }

        }
    }

    private function actionGis2019(){

        $old_email = ArrayHelper::getColumn(gisParseEmail::find()->all(),'email');
        $old_site = ArrayHelper::getColumn(gisParseSite::find()->all(),'url');

        $newFn = 'Kirov2gis.csv';


        $lines = file($newFn,FILE_IGNORE_NEW_LINES);


        foreach ($lines as $key => $value)
        {

            $row = explode(';',$value);
            if ( $key < 2) continue;

            if (count($row) == 14){
                $cat = str_replace('"','',$row[12]);
                $subcat = str_replace('"','',$row[13]);
                $url = trim(str_replace('"','',$row[7]));
                $emails = trim(str_replace('"','',$row[6]));
            } elseif(count($row) == 15) {
                $cat = str_replace('"','',$row[13]);
                $subcat = str_replace('"','',$row[14]);
                $url = trim(str_replace('"','',$row[8]));
                $emails = trim(str_replace('"','',$row[7]));
            }else {
                continue;
            }


            $row[0] = trim(str_replace('"','',$row[0]));


            if ( $url && !in_array($url,$old_site)){
                $rec = new gisParseSite();
                $rec->url = $url;
                $rec->cat = $cat;
                $rec->subcat = $subcat;
                $rec->save();
            }


            if (!$emails) continue;
            $emails = explode(',',$emails);

            foreach ($emails as $email){
                if (! in_array($email,$old_email)) {
                    $rec = new gisParseEmail();
                    $rec->email = $email;
                    $rec->cat = $cat;
                    $rec->subcat = $subcat;
                    $rec->ext_id = $row[0];
                    $rec->save();
                }
            }

        }
    }


    public function actionTestTime(){

        $this->layout = false;
        return $this->render('time');
    }

    public function actionTest(){

        $this->layout = false;
        return $this->render('test');
    }

    public function actionUpdate()
    {
        $settings = Car::find()->indexBy('id')->limit(1)->all();



        if (Model::loadMultiple($settings, Yii::$app->request->post()) && Model::validateMultiple($settings)) {
            foreach ($settings as $setting) {
                $setting->save(false);
            }
            return $this->redirect('index');
        }

        return $this->render('update', ['settings' => $settings]);
    }

    public function actionWp(){

        $data = [
         //   'title'=>'new post 777000' ,
            //'post_views'=>42342342,
        //    '_aioseop_title'=>'nice title 4',
          //  '_aioseop_description'=>'nice 3 '
            'author'=>1,
            'content'=>1,
            'post'=>55,
            'rating'=>5
        ];

        $process = curl_init('http://wordpress/wp-json/wp/v2/comments');
        //curl_setopt($process, CURLOPT_HTTPHEADER, array('Content-Type: application/xml', $additionalHeaders));
        curl_setopt($process, CURLOPT_HEADER, 1);
        curl_setopt($process, CURLOPT_USERPWD, 'admin' . ":" . 'qwerty');
        curl_setopt($process, CURLOPT_TIMEOUT, 30);
        curl_setopt($process, CURLOPT_POST, 1);
        curl_setopt($process, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
        $return = curl_exec($process);
        ex($return);
        curl_close($process);
    }


    public function actionIndex()
    {

        return $this->render('index');
    }

    public function actionPay(){
        $data_post = array (
            'notification_type' => 'card-incoming',
            'zip' => '',
            'amount' => '1.96',
            'firstname' => '',
            'codepro' => 'false',
            'withdraw_amount' => '2.00',
            'city' => '',
            'unaccepted' => 'false',
            'label' => '29',
            'building' => '',
            'lastname' => '',
            'datetime' => '2018-03-08T20:17:29Z',
            'suite' => '',
            'sender' => '',
            'phone' => '',
            'sha1_hash' => '17fc8925d280aa5472d3102bf99de5d32c2ad693',
            'street' => '',
            'flat' => '',
            'fathersname' => '',
            'operation_label' => '2233adff-0002-5000-8036-053b8e7edb6c',
            'operation_id' => '573855449753013012',
            'currency' => '643',
            'email' => '',
        );

        $urlTest = 'http://192.168.0.152/payment/yandex/message';
        // Get cURL resource
        $curl = curl_init();
    // Set some options - we are passing in a useragent too here
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $urlTest,
                CURLOPT_USERAGENT => 'Codular Sample cURL Request',
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => $data_post
            ));
    // Send the request & save response to $resp
            $resp = curl_exec($curl);
        $http_code = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
        ex([
            $resp,
            $http_code
        ]);
    // Close request to clear up some resources
            curl_close($curl);
            //$http_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

    }

    public function actionSitemapNot200(){



        $se = new SiteError();
        $se->actionSitemapNot200();

        $text =  $se->resultTextFormat();
        $settings = \Yii::$app->getModule('settings');

        $lastDateTime = $settings->editVar( 'siteErrorLastDateTime',time());
        $siteErrorLastCheck  = $settings->editVar( 'siteErrorLastCheck',$text);

        return $text;
        /*$this->getLoadSiteMap();
        ex($this->sites);*/
    }

    private function getLoadSiteMap(){
        foreach ( $this->sites as $num => $item){

            $siteMap = $this->url_load( $item['sitemap'] );
            $xml = new \SimpleXMLElement($siteMap['respond']);

            foreach ($xml->url as $url_list) {
                $url = (string)$url_list->loc;
                if ( $url && $this->validUrl($url) ) {
                    $raw = $this->url_load($url);

                    if ($raw['http_code'] == "200" ||
                        $raw['http_code'] == "301" ||
                        $raw['http_code'] == "302"
                    ){ // 200
                        $this->sites[$num]['static']['count200'] += 1;
                    } else { // err
                        $this->sites[$num]['static']['countErr'] += 1;
                    }

                    $rec = [
                        'url'=>$url,
                        'code'=>$raw['http_code']
                    ];
                    $this->sites[$num]['loadLinks'][] = $rec;
                } else {
                    var_dump('no');
                }


            }

        }
    }

    private function validUrl($url){
        return (  ! filter_var($url, FILTER_VALIDATE_URL) === FALSE) ;
    }

    private function url_load($url=''){
        $timeout = 10;
        $ch = curl_init();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_TIMEOUT, $timeout );
        $http_respond = curl_exec($ch);
        //$http_respond = trim( strip_tags( $http_respond ) );
        $http_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

        /*if ( ( $http_code == "200" ) || ( $http_code == "302" ) ) {
            return true;
        } else {
            // return $http_code;, possible too
            return false;
        }*/
        curl_close( $ch );

        return [
            'respond'=>$http_respond,
            'http_code'=>$http_code
        ];
    }


}
