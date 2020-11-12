<?php

namespace app\modules\news\models;

use app\modules\image\models\ImgLinks;
use backend\models\appclass\AttImg;
use Yii;

/**
 * This is the model class for table "new_block".
 *
 * @property integer $id
 * @property string $name
 * @property string $desc
 * @property integer $news_id
 * @property integer $ord
 * @property integer $status
 * @property integer $type_block
 *
 *
 */
class NewsBlock extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */

    const ST_OK = 1;
    const ST_NO = 2;

    const TB_INTRO = 1;
    const TB_INTRO_INVERT = 2;
    const TB_TWO_IMG_TXT = 3;
    const TB_TWO_TXT = 4;
    const TB_BANNER = 5;

    public static  $arrTxtStatus = [1 => 'Опубликован',2 =>'Нет'];

    public static  $arrTxtBlock = [
        self::TB_INTRO => 'Заглавный',
        self::TB_INTRO_INVERT => 'Заглавный инвертированный',
        self::TB_TWO_IMG_TXT => 'Второстепенный с картинкой',
        self::TB_TWO_TXT => 'Второстепенный только текст',
        self::TB_BANNER => 'Баннер',


    ];

    public function getNews_r(){

        return $this->hasOne( News::className(), ['id' => 'news_id'] );
    }


    public static function tableName()
    {
        return 'news_block';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['news_id','type_block'], 'required'],
            [['news_id', 'ord','type_block','status'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['desc'], 'string', 'max' => 9000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'desc' => 'Описание',
            'news_id' => 'К какой нововсти',
            'type_block' => 'Тип бока',
            'ord' => 'Порядок',
            'status' => 'Статус'
        ];
    }

    public function getImgList($limit=false,$test=null){

        $imgL = ImgLinks::find()->
        //select('img_links.id,img_links.type,img_links.id_type,img_links.id_image,img_links.ord,img.id,img.width,img.name_image,img.alt,img.title')->
        innerJoin('img','img_links.id_image = img.id')->
        where(
            [
                'img_links.id_type'=>$this->id,
                'img_links.type'=>'news_bl',
                //    'img.width'=>$size,

            ]
        )->andWhere(['!=','img.parent_id',0])->
        orderBy(['img.ord' =>SORT_ASC]);
        //  orderBy(['img_links.ord' =>SORT_ASC]);

        if ($limit){
            $imgL->limit($limit);
        }
        if ($test != null){
            var_dump( $imgL->createCommand()->rawSql );
            //exit;

        }
        return $imgL->all();

        //  echo $imgL->createCommand()->getRawSql();

    }



}
