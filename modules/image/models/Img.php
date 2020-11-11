<?php

namespace app\modules\image\models;

use Yii;
use yii\helpers\Html;
use yii\helpers\Inflector;

/**
 * This is the model class for table "img".
 *
 * @property integer $id
 * @property string $filename
 * @property integer $width
 * @property integer $height
 * @property string $date_cr
 * @property string $alt
 * @property string $title
 * @property integer $ord
 * @property integer $status
 * @property integer $parent_id
 * @property string $name_image
 * @property integer $watermark
 * @property integer $logo_r_b
 * @property integer $optimize
 * @property integer $blur
 *
 *
 *
 */
class Img extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */

    const  form_path = '@app/modules/image/views/image/img_form';

    public  $crop_x = 0;
    public  $crop_y = 0;
    public  $crop_width = 0;
    public  $crop_height = 0;

    public  $wrap_width = 0;
    public  $wrap_height = 0;

    public $free_size = 0;
    public $resize = 0;
    public $restore = 0;
    public $harshness = 0;
    public $blur = 0;
    public $update_img = 0;
    public $errors=0;
    public $imgs = 0;



    public $size = '';




    const NOIMG ='images/noimage/noname.png';
    const NOIMG_BIG ='images/noimage/noimage.jpg';


    public static function tableName()
    {
        return 'img';
    }

    public function attributes()
    {
        return array_merge(
            parent::attributes(),
            ['resize','crop_x','crop_y','crop_width','crop_height','wrap_width','wrap_height','harshness',
                'blur','update_img','size','restore' ]
        );

    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['filename'], 'required'],

            [['width', 'height', 'ord', 'status',
                'parent_id', 'watermark',
                'crop_x','crop_y','crop_width','crop_height','wrap_height','wrap_width',
                'original','resize','fullsize','restore','logo_r_b','optimize','harshness',
                'blur'
            ], 'integer'],

            [['filename', /* 'alt', 'title'*/'size'], 'string', 'max' => 255],
            [['date_cr' ,'alt','title' ,'size','resize' ], 'safe'],
            [['name_image'], 'string', 'max' => 255],
            [['name_image' ], 'string', 'max' => 255],



            [['status','watermark','fullsize','logo_r_b','optimize','original' ], 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'filename' => 'Filename',
            'width' => 'Width',
            'height' => 'Height',
            'date_cr' => 'Date Cr',
            'alt' => 'Alt',
            'title' => 'Title',
            'ord' => 'Ord',
            'status' => 'Status',
            'parent_id' => 'Parent ID',
            'name_image' => 'Name Image',
            'watermark' => 'Водяной знак',
            'original' => 'Оригинал',
            'update_img' => 'Обновить файл',
            'restore' => 'Востановить из оригинала',
            'logo_r_b'=>'Лого',
            'optimize'=>'Оптимизация',
            'harshness' => 'Резкость',
            'blur' => 'Размытие'
        ];
    }

    public function getPathImg($id_type,$type='products',$size='orig'){

        return $this->name_image;
    }

    public function getImgLink_r(){
        return $this->hasOne( ImgLinks::class, ['id_image' => 'id']);
    }

    public function getParent_r(){
        return $this->hasOne( Img::class,['id' => 'parent_id']);
    }


    public function getOriginal(){

        if ($this->parent_id == 0 && $this->original){

            return $this;
        } else {
            $parent = Img::findOne(['id'=>$this->parent_id]);

            if (is_object($parent) && $parent->original){
                return $parent;
            }
        }
        /* var_dump('not found');
         ex($this);*/
        return null;
    }

    public function getFullSizeItem(){
        if ($this->fullsize) return $this;
        if ($this->parent_id){
            $imgq = Img::find()->where(['parent_id'=>$this->parent_id,'fullsize'=>1]);

            $img = $imgq->one();
            if ($img !== null) return $img;

        } else {
            $imgq = Img::find()->where(['parent_id'=>$this->id,'fullsize'=>1]);
            $img = $imgq->one();
            if ($img !== null) return $img;

        }
        return null;
    }


    public static function getImgEntFirst($type,$id_type,$trace=false){

        $imgL = ImgLinks::find()->
        select('img_links.id,img_links.type,img_links.id_type,
        img_links.id_image,img_links.ord,img.id,img.width,img.name_image,img.parent_id')->
        innerJoin('img','img_links.id_image = img.id')->// leftJoin('img','img_links.id_image = img.id')->
        where(
            "(`img_links`.`id_type` = :idtype )   AND 
        (`img_links`.`type` = :type ) "
            ,
            [
                'idtype'=> $id_type,
                'type'=> $type,

            ]
        )
            ->
            orderBy(['img.width' =>SORT_ASC ,'img.ord' =>SORT_ASC ,'img.width' =>SORT_DESC]);

        if ($trace){
            var_dump(   $imgL->createCommand()->rawSql) ; exit;
        }

        return $imgL->one();


    }

    // !! del
    public function getPreviewImgSize($size){
        if ($this->parent_id == 0) {
            $imgs  = Img::find()->where(['parent_id'=>$this->id])
                ->andWhere(['width'=> $size])->all();
            return $imgs;
        }
        return null;

    }

    public static function getImgList($id_type,$type,$size){

        $imgL = ImgLinks::find()->
        select('img_links.id,img_links.type,img_links.id_type,img_links.id_image,img_links.ord,img.id,img.width,img.name_image,img.alt,img.title,img.parent_id')->
        innerJoin('img','img_links.id_image = img.id')->// leftJoin('img','img_links.id_image = img.id')->
        where(
            [
                'img_links.id_type'=>$id_type,
                'img_links.type'=>$type,
                'img.width'=>$size,

            ]
        )->andWhere(['!=','img.parent_id',0])->
        orderBy(['img.ord' =>SORT_ASC])->asArray()->all();
        //  var_dump($imgL);exit;
        return $imgL;
    }

    public static function getImgMain($size,$type,$id_type,$trace=false){
        $paramsQ =  [
            'img_links.id_type'=> $id_type,
            'img_links.type'=> $type,
            'img.original'=>0,
            'img.fullsize'=>0,
            'img.webp'=>0
        ];

        if (is_array($size)){
            $paramsQ = array_merge($paramsQ,$size);
        } else {
            $paramsQ['img.width'] = $size;
        }
        //ex($paramsQ);

        $imgL = ImgLinks::find()->
        select('img_links.id,img_links.type,img_links.id_type,
        img_links.id_image,img_links.ord,img.id,img.width,img.name_image,img.parent_id')->
        innerJoin('img','img_links.id_image = img.id')->
        andWhere($paramsQ)->
        orderBy(['img.ord' =>SORT_ASC ,'img.width' =>SORT_DESC]);
        if ($trace){
           // ex($paramsQ);
            var_dump(   $imgL->createCommand()->rawSql) ; exit;
        }

        return $imgL->one();
        /*  if  ( $obj = $imgL->one() !== null )
          {
              return $obj;
          } else {
              return new ImgLinks();
          }*/

    }

    public static function getImgMainByHeight($size,$type,$id_type,$trace=false){
        $imgL = ImgLinks::find()->
        select('img_links.id,img_links.type,img_links.id_type,
        img_links.id_image,img_links.ord,img.id,img.width,img.height,img.name_image,img.parent_id')->
        innerJoin('img','img_links.id_image = img.id')->// leftJoin('img','img_links.id_image = img.id')->
        where(
            "(`img_links`.`id_type` = :idtype )   AND 
        (`img_links`.`type` = :type )   AND
        (`img`.`height` = :height) "
            //OR `img`.`parent_id` = 0
            ,
            /*[
                'img_links.id_type'=>$this->id,
                'img_links.type'=>'products',
                'img.width'=>$size
            ]*/
            [
                'idtype'=> $id_type,
                'type'=> $type,
                'height'=> $size,
            ]
        )->
        // orderBy(['img_links.ord' =>SORT_ASC ,'img.width' =>SORT_DESC]);
        orderBy(['img.ord' =>SORT_ASC ,'img.width' =>SORT_DESC]);
        if ($trace){
            var_dump(   $imgL->createCommand()->rawSql) ; exit;
        }

        return $imgL->one();
        /*  if  ( $obj = $imgL->one() !== null )
          {
              return $obj;
          } else {
              return new ImgLinks();
          }*/

    }



    public function beforeSave($insert)
    {

        if ($this->isNewRecord){
            $this->date_cr = date('Y-m-d H:i:s');
        }
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function getSrc(){

        return '/' . $this->name_image;
    }

    public function getAlt(){

        return Html::encode( $this->alt );
    }

    public function getTitle(){

        return Html::encode( $this->title );
    }


    public function getWebPItem(){
        if ($this->webp) return $this;
        if ($this->parent_id){
            $imgq = Img::find()->where(['parent_id'=>$this->parent_id,'webp'=>1]);

            $img = $imgq->one();
            if ($img !== null) return $img;

        } else {
            $imgq = Img::find()->where(['parent_id'=>$this->id,'webp'=>1]);
            $img = $imgq->one();
            if ($img !== null) return $img;

        }
        return null;
    }

}
