<?php
/**
 * Created by PhpStorm.
 * User: o.trushkov
 * Date: 26.04.18
 * Time: 10:50
 */

namespace app\modules\image\services;


class ImgOptim
{

    private static $client = NULL;

    private $new_url = null;

    public $q=100;


    public static function getClient() {


        if (!self::$client) {
            self::$client = new ImgOptimClient( );
        }

        return self::$client;
    }

    function fromFile($path) {

        $string = file_get_contents($path);

        $response = $this->request("post", "/preseach", $string,['quality' => $this->q]);

        $this->new_url = $response->headers["location_new"];

    }

    function toFile($to){
        file_put_contents($to,file_get_contents($this->new_url));
        return filesize($to);
    }

    private  function request($method, $url, $body = NULL, $header = array()) {
        ini_set(' client_max_body_size','20');



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



                if (isset($headers["compression-count"])) {
                    // Tinify::setCompressionCount(intval($headers["compression-count"]));
                }

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
                throw new \Exception($details->message . ' ' .  $details->error . ' ' .$status);
            } else {
                $message = sprintf("%s (#%d)", curl_error($request), curl_errno($request));
                curl_close($request);
                if ($retries > 0) continue;
                throw new \Exception("Error while connecting: " . $message);
            }
        }
    }



}




