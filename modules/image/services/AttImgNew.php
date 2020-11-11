<?php

namespace app\modules\image\services;

class AttImgNew
{

    private $params = [];
    private $image_type;
    private $image_id;

    public function __construct($params,$image_type, $image_id)
    {
        $this->params = $params;
        $this->image_type = $image_type;
        $this->image_id = $image_id;

    }

    public function preseachImgNew($image_type, $image_id)
    {

        $field = 'Imgnew';

        if (!isset($_FILES[$field])) throw new \Exception("Not isset files");
        if (!$_FILES[$field]) throw new \Exception("Empty files");

        $files = array($field => array());

        if (!is_array($_FILES[$field]["error"])) {
            foreach ($_FILES[$field] as $key => $value) {
                $files[$field][$key] = array($value);
            }
        } else {
            $files = $_FILES;
        }

        foreach ($files[$field]["error"] as $key => $error) {
            if ($error == UPLOAD_ERR_OK) {
                $tmp_name = $files[$field]["tmp_name"][$key];
                $file_type = mime_content_type($tmp_name);
                $mime_types = ["image/gif","image/jpg","image/jpeg","image/png","image/bmp"];

                if (FALSE == in_array($file_type, $mime_types[$file_type])) {
                    throw new \Exception("Invalid file MIME type");
                }

            }
        }
        ex($files);

    }


}