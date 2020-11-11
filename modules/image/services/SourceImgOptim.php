<?php
/**
 * Created by PhpStorm.
 * User: o.trushkov
 * Date: 26.04.18
 * Time: 10:53
 */

namespace app\modules\image\services;


class SourceImgOptim
{
    private $url, $commands;



    public static function fromFile($path) {

        return self::fromBuffer(file_get_contents($path));
    }

    public static function fromBuffer($string) {

        $string = file_get_contents($string);

        $response = ImgOptim::getClient()->request("post", "/preseach", $string,['quality: ']);


        return new self($response->headers["location_new"]);
    }

    public function __construct($url, $commands = array()) {
        $this->url = $url;
        $this->commands = $commands;
    }

    public function toFile($path) {
        return $this->result()->toFile($path);
    }

    public function result() {
       // $response = ImgOptim::getClient()->request("get", $this->url, $this->commands);
        return new Result(file_get_contents($this->url));
    }

 /*   public static function fromUrl($url) {
        $body = array("source" => array("url" => $url));
        $response = Tinify::getClient()->request("post", "/shrink", $body);
        return new self($response->headers["location"]);
    }


    public function preserve() {
        $options = $this->flatten(func_get_args());
        $commands = array_merge($this->commands, array("preserve" => $options));
        return new self($this->url, $commands);
    }

    public function resize($options) {
        $commands = array_merge($this->commands, array("resize" => $options));
        return new self($this->url, $commands);
    }

    public function store($options) {
        $response = Tinify::getClient()->request("post", $this->url,
            array_merge($this->commands, array("store" => $options)));
        return new Result($response->headers, $response->body);
    }





    public function toBuffer() {
        return $this->result()->toBuffer();
    }

    private static function flatten($options) {
        $flattened = array();
        foreach ($options as $option) {
            if (is_array($option)) {
                $flattened = array_merge($flattened, $option);
            } else {
                array_push($flattened, $option);
            }
        }
        return $flattened;
    }*/
}

