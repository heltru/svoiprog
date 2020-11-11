<?php
/**
 * Created by PhpStorm.
 * User: o.trushkov
 * Date: 26.04.18
 * Time: 15:16
 */

namespace app\modules\image\services;


use app\modules\helper\models\Helper;

class ImageClinet
{
    const API_ENDPOINT = "https://image.convert.aim/image";//"http://image.it-06.aim/image";
//https://image.convert.aim/
    const RETRY_COUNT = 1;
    const RETRY_DELAY = 500;


    private $options;

    function __construct() {



        $this->options = array(
            CURLOPT_BINARYTRANSFER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            //CURLOPT_USERPWD => "api:" . $key,
            // CURLOPT_CAINFO => self::caBundle(),
            //CURLOPT_SSL_VERIFYPEER => true,
            // CURLOPT_USERAGENT => join(" ", array_filter(array(self::userAgent(), $app_identifier))),
        );


    }

    public function sendImage($src,$q=null){

        $url_img =  \Yii::$app->request->hostInfo. '/' . $src;


        $body = [
            'name_img' =>basename($src),
            'mime_content_type'=>mime_content_type($src),
            'url_img'=>$url_img
        ];

        if ($body['mime_content_type'] == 'image/jpeg' && $q ){
            $body['quality'] = $q;
        }




        $res = $this->request('post','/preseach',$body);

//ex($res);

        if (isset($res->headers['location_new'])){
            return $res->headers['location_new'];
        }

        return null;


    }

    public function loadAndUpdate($url,$to){

        file_put_contents($to, file_get_contents($url));
    }

    public function request($method, $url, $body = NULL) {
        $header = array();
        if (is_array($body)) {
            if (!empty($body)) {
                $body = json_encode($body);
                array_push($header, "Content-Type: application/json");
            } else {
                $body = NULL;
            }
        }

        for ($retries = self::RETRY_COUNT; $retries >= 0; $retries--) {
            if ($retries < self::RETRY_COUNT) {
                usleep(self::RETRY_DELAY * 1000);
            }

            $request = curl_init();
            if ($request === false || $request === null) {
                throw new \Exception(
                    "Error while connecting: curl extension is not functional or disabled."
                );
            }

            curl_setopt_array($request, $this->options);

            $url = strtolower(substr($url, 0, 6)) == "https:" ? $url : self::API_ENDPOINT . $url;
            curl_setopt($request, CURLOPT_URL, $url);
            curl_setopt($request, CURLOPT_CUSTOMREQUEST, strtoupper($method));

            if (count($header) > 0) {
                curl_setopt($request, CURLOPT_HTTPHEADER, $header);
            }

            if ($body) {
                curl_setopt($request, CURLOPT_POSTFIELDS, $body);
            }



            $response = curl_exec($request);




            if (is_string($response)) {
                $status = curl_getinfo($request, CURLINFO_HTTP_CODE);
                $headerSize = curl_getinfo($request, CURLINFO_HEADER_SIZE);
                curl_close($request);

                $headers = self::parseHeaders(substr($response, 0, $headerSize));
                $body = substr($response, $headerSize);



                if ($status >= 200 && $status <= 299) {
                    return (object) array("body" => $body, "headers" => $headers);
                }

                $details = json_decode($body);
                if (!$details) {
                    $message = sprintf("Error while parsing response: %s (#%d)",
                        PHP_VERSION_ID >= 50500 ? json_last_error_msg() : "Error",
                        json_last_error());
                    $details = (object) array(
                        "message" => $message,
                        "error" => "ParseError"
                    );
                }

                if ($retries > 0 && $status >= 500) continue;

                throw new \Exception($details->message,$status); // Exception::create($details->message, $details->error, $status);
            } else {
                $message = sprintf("%s (#%d)", curl_error($request), curl_errno($request));
                curl_close($request);
                if ($retries > 0) continue;
                throw new ConnectionException("Error while connecting: " . $message);
            }
        }
    }

    protected static function parseHeaders($headers) {
        if (!is_array($headers)) {
            $headers = explode("\r\n", $headers);
        }

        $res = array();
        foreach ($headers as $header) {
            if (empty($header)) continue;
            $split = explode(":", $header, 2);
            if (count($split) === 2) {
                $res[strtolower($split[0])] = trim($split[1]);
            }
        }
        return $res;
    }



}