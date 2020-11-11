<?php

namespace app\modules\admin\models;

use yii\base\Model;
use yii\web\UploadedFile;

class UploadVideoForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $videoFile;

    public function rules()
    {
        return [
            [['videoFile'], 'file', 'skipOnEmpty' => false, /*'extensions' => 'png, jpg'*/],
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $this->videoFile->saveAs(VIDEO_PATH .'/' . $this->videoFile->baseName . '.' . $this->videoFile->extension);
            return true;
        } else {
            return false;
        }
    }
}