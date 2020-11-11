<?php

namespace app\modules\url\models;

use Yii;

/**
 * This is the model class for table "url_redirect".
 *
 * @property integer $id
 * @property string $url_in
 * @property string $url_out
 * @property integer $status_res
 */
class UrlRedirect extends \yii\db\ActiveRecord
{


  //  public $status_res;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'url_redirect';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

            [['url_in', 'url_out'], 'string', 'max' => 511],

            [['status'], 'integer'],

            [['status'], 'default','value'=>0],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'url_in' => 'Входящий',
            'url_out' => 'Исходящий',
            'status' => 'Статус',
        ];
    }


}
