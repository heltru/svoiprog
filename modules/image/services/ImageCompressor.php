<?php


namespace app\modules\image\services;


use yii\helpers\Json;

class ImageCompressor
{

    private $url_api_preseach = 'http://image.convert.aim/image/nimage';
    private $url_api_download = 'http://image.convert.aim/image/nimagedownloadsingle?name=';


    public $setting = ['web_b_convert'=>0,'mode_resize'=>'resize_auto','optimize'=>0];


    private $files=[];


    private $result=[];

    public function getResult(){
        return $this->result;
    }

    public function __construct($files)
    {
        $this->files = $files;

    }


    private function getFiles(){
        return $this->files;
    }

    private function getSetting($name){
        return $this->setting[$name];
    }

    public function optimize_photo(){

        $target_url =  $this->url_api_preseach;

        $fields = [];
        foreach ($this->setting as $set_name => $set_val){
            $fields['NImageForm['.$set_name.']'] = $set_val;
        }

        $files_send = [];
        foreach ($this->getFiles() as $num => $file){
            $files_send["NImageForm[]"] = [
                'name'=>$file['name'],
                'content'=>$file['content']
            ];
        }



        $curl = curl_init();

        $boundary = uniqid();
        $delimiter = '-------------' . $boundary;

        $post_data = $this->build_data_files($boundary, $fields, $files_send);
        //ex($target_url);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $target_url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $post_data,
            CURLOPT_HTTPHEADER => array(
                "Content-Type: multipart/form-data; boundary=" . $delimiter,
                "Content-Length: " . strlen($post_data)
            )));


        $res = null;


        if( ! $res = curl_exec($curl))
        {
            try {
                trigger_error(curl_error($curl));

            } catch (\Exception $e) {

                $res =  null;
                return false;
            }
        }
        curl_close($curl);
        try {
            $res = Json::decode( $res,true);
            $this->result = $res;

        } catch (\Exception $e) {
            return false;
            //throw new BadRequestHttpException('Ошибка данных');
        }


        if (isset($res['id'])){
            return true;
        }

        return false;
    }

    private function build_data_files($boundary, $fields, $files){
        $data = '';
        $eol = "\r\n";

        $delimiter = '-------------' . $boundary;

        foreach ($fields as $name => $content) {
            $data .= "--" . $delimiter . $eol
                . 'Content-Disposition: form-data; name="' . $name . "\"".$eol.$eol
                . $content . $eol;
        }


        foreach ($files as $name => $item) {
            $data .= "--" . $delimiter . $eol
                . 'Content-Disposition: form-data; name="' . $name . '"; filename="' . $item['name'] . '"' . $eol
                . 'Content-Transfer-Encoding: binary'.$eol
            ;

            $data .= $eol;
            $data .= $item['content'] . $eol;
        }


        $data .= "--" . $delimiter . "--".$eol;


        return $data;
    }

}