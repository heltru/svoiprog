<?php

namespace app\modules\image\models;

use Yii;

/**
 * This is the model class for table "img_links".
 *
 * @property integer $id
 * @property string $type
 * @property integer $id_type
 * @property integer $id_image
 */
class ImgLinks extends \yii\db\ActiveRecord
{


    const T_Wm = 'watermark';
    const T_Ns = 'news';
    const T_Cy = 'cat';
    const T_NsBl = 'news_bl';





    public static  $arrTxtStatus = [0 => 'Включен',1 =>'Выключен'];


    public static function tableName()
    {
        return 'img_links';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'string'],
            [['id_type', 'id_image','ord'], 'integer'],
        ];
    }

    public function getImg_r(){
        return $this->hasOne( Img::class, ['id' => 'id_image']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'id_type' => 'Id Type',
            'id_image' => 'Id Image',
        ];
    }
}
