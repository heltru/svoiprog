<?php
/**
 * Created by PhpStorm.
 * User: heltru
 * Date: 18.09.2018
 * Time: 0:36
 */

namespace app\modules\helper\models;


use app\modules\google\controllers\backend\GoogleController;
use app\modules\helper\components\Transliteration;
use app\modules\user\models\User;
use yii\web\ForbiddenHttpException;

class Helper
{


    public static $quarter_ru = [
        1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV'
    ];

    public static $mont_ru = [
        1 => 'янв', 2 => 'фев', 3 => 'мар', 4 => 'апр',
        5 => 'май', 6 => 'июн', 7 => 'июл', 8 => 'авг',
        9 => 'сен', 10 => 'окт', 11 => 'ноя', 12 => 'дек'
    ];

    public static $week_ru = [
        1 => 'Пн', 2 => 'Вт', 3 => 'Ср', 4 => 'Чт',
        5 => 'Пт', 6 => 'Сб', 0 => 'Вс'
    ];

    public static function download_send_headers($filename)
    {
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


    public static function array2csv(array &$array)
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


    public static function base64_to_jpeg($data, $output_file)
    {
        // open the output file for writing
        list($type, $data) = explode(';', $data);
        list(, $data) = explode(',', $data);
        $data = base64_decode($data);

        file_put_contents($output_file, $data);

    }

    public static function text_to_file($data, $output_file)
    {
        file_put_contents($output_file, $data);
    }

    public static function mysql_datetime($now_timestamp = false)
    {
        if ($now_timestamp === false) {
            return date('Y-m-d H:i:s');
        } else {
            return date('Y-m-d H:i:s', $now_timestamp);
        }

    }

    public static function getIsAdmin($user_id)
    {
        $items = \Yii::$app->authManager->getRolesByUser($user_id);
        return array_key_exists('admin', $items);
    }

    public static function checkDevicePerson($persona_id){
        if (self::getIsAdmin(\Yii::$app->user->id)){
            return true;
        }
        $personaIdCheck  = \Yii::$app->getModule('app')->getCompanyId();

        if ($personaIdCheck !=  $persona_id){
            throw new ForbiddenHttpException('Access Denied');
        }
    }

    public static function runConsole($command)
    {

        $cmd = $command;

        $cmd = "{$cmd} > /dev/null 2>&1 &";

        pclose(popen($cmd, 'r'));
        return true;


    }


    public static function genColorCodeFromText($text, $min_brightness = 100, $spec = 10, $crc = true)
    {
        $err = '000000';
        // Check inputs
        if (!is_int($min_brightness)) return $err;
        if (!is_int($spec)) return $err;
        if ($spec < 2 or $spec > 10) return $err;
        if ($min_brightness < 0 or $min_brightness > 255) return $err;


        $hash = ($crc) ? md5(crc32($text)) : md5($text);
        //$hash =  md5( crc32($text));//md5($text);  //Gen hash of text

        $colors = array();
        for ($i = 0; $i < 3; $i++)
            $colors[$i] = max(array(round(((hexdec(substr($hash, $spec * $i, $spec))) / hexdec(str_pad('', $spec, 'F'))) * 255), $min_brightness)); //convert hash into 3 decimal values between 0 and 255

        if ($min_brightness > 0)  //only check brightness requirements if min_brightness is about 100
            while (array_sum($colors) / 3 < $min_brightness)  //loop until brightness is above or equal to min_brightness
                for ($i = 0; $i < 3; $i++)
                    $colors[$i] += 10;    //increase each color by 10

        $output = '';

        for ($i = 0; $i < 3; $i++)
            $output .= str_pad(dechex($colors[$i]), 2, 0, STR_PAD_LEFT);  //convert each color to hex and append to output

        return $output;
    }

    public static function nophoto()
    {
        return '/images/noimage/noimage.jpg';
    }


    public static function transliterate($txt)
    {


        $smth = preg_replace("/%u([0-9a-f]{3,4})/i", "&#x\\1;", urldecode($txt));
        $txt = html_entity_decode($smth, null, 'UTF-8');
        $txt = mb_strtolower($txt);


        $replacement = '-';
        $translator = new Transliteration();
        $translator->standard = Transliteration::GOST_779B;

        $txt = $translator->transliterate($txt);

        $string = preg_replace('/[^a-zA-Z0-9=\s—–-]+/u', '', $txt);
        $string = preg_replace('/[=\s—–-]+/u', $replacement, $string);
        $txt = trim($string, $replacement);
        return $txt;
    }

    public static function curl_get($url, $query_data, $options = [], $deb = false)
    {


        if ($deb) {
            ex($url . (strpos($url, '?') === FALSE ? '?' : '') . http_build_query($query_data));
        }

        $defaults = array(
            CURLOPT_URL => $url . (strpos($url, '?') === FALSE ? '?' : '') . http_build_query($query_data),
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT => 10, //Максимальное количество секунд для выполнения функций cURL
            CURLOPT_CONNECTTIMEOUT => 10, //Количество секунд ожидания при попытке подключения,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,

        );


        $ch = curl_init();
        curl_setopt_array($ch, ($options + $defaults));
        if (!$result = curl_exec($ch)) {
            try {
                trigger_error(curl_error($ch));


            } catch (\Exception $e) {
                curl_close($ch);
                return null;
            }

        }
        curl_close($ch);
        return $result;
    }


    /**
     *
     * @return \Google_Client client
     */
    public static function getGoogleClient()
    {
        $user = User::findOne(['id' => \Yii::$app->user->id]);

        $client = new \Google_Client();
        $client->setAccessType('offline');
        $client->setApprovalPrompt("force");
        $client->setAuthConfig('oauth-credentials.json');

        $client->addScope([
            \Google_Service_Sheets::SPREADSHEETS
        ]);

        $token = json_decode($user->goo_token, true);


        if ($token == null){
            header('Location: /admin/google/google/login');
            exit;


        }

        $client->setAccessToken(json_decode($user->goo_token, true));

        // Если время жизни токена истекло, обновляем его
        if ($client->isAccessTokenExpired()) {

            // При обновлении токена refresh токен отсутствует
            // Поэтому сохраняем refresh токен в отдельную переменную
            $refreshTokenSaved = $client->getRefreshToken();

            // Обновляем токен
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());

            // Создаём новую переменную, в которую помещаем новый обновлённый токен
            $accessTokenUpdated = $client->getAccessToken();

            // Добавляем в эту переменную старый refresh токен
            $accessTokenUpdated['refresh_token'] = $refreshTokenSaved;

            // Сохраняем в сессии новый токен и старый refresh токен

            // Устанавливаем новый токен
            $client->setAccessToken($accessTokenUpdated);
            $user->goo_token = json_encode($accessTokenUpdated);
            $user->update(false,['goo_token']);
        }
        return $client;
    }

    public static  function is_html($string)
    {
        return preg_match("/<[^<]+>/",$string,$m) != 0;
    }




}