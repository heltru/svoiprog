<?php

namespace app\modules\textpage\models;

use app\modules\url\models\Url;
use Yii;

/**
 * This is the model class for table "textpage".
 *
 * @property integer $id
 * @property string $name
 * @property integer $status
 * @property integer $text
 * @property integer $type_page
 * @property integer $module
 *
 */
class Textpage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */


    const ST_OK = 0;
    const ST_NO = 1;

    const SM_OK = 1;
    const SM_NO = 0;

    const M_Cat = 'catalog';
    const M_Blc = 'blogcat';
    const M_Txp = 'textpage';
    const M_Bst = 'basket';
    const M_Geo = 'geo';
    const M_SV = 'servcenter';

    const TP_Mn = 'main';
    const TP_Fr = 'search';
    const TP_Bt = 'view';
    const TP_Bg = 'blog';
    const TP_Dy = 'delivery';
    const TP_Py = 'payment';
    const TP_Ho = 'howorder';
    const TP_Fq = 'faq';
    const TP_Or = 'view';
    const TP_As = 'actions';
    const TP_Bd = 'brands';
    const TP_Ct = 'contact';
    const TP_At = 'about';
    const TP_Cs = 'certs';
    const TP_Oi = 'orderinfo';
    const TP_Ab = 'allbrands';
    const TP_Sc = 'searchsite';
    const TP_Lm = 'linkmap';
    const TP_Sb = 'search';
    const TP_Do = 'oblast';
    const TP_Dc = 'city';
    const TP_Ds = 'delivsputnik';
    const TP_Er = '404';
    const TP_ScM = 'servicecenter_main';
    const TP_SO = 'sellout';



    public static $arrModules = [
        self::M_Cat => 'категория товара',
        self::M_Blc => 'категория блога' ,
        self::M_Txp => 'страницы сайта' ,
        self::M_Bst => 'корзина' ,
        self::M_Geo => 'гео' ,
        self::M_SV => 'servcenter' ,
    ];



    public static $arrTxtTypePage = [
        self::M_Cat => [
             self::TP_Fr => 'Поиск по каталогу'
        ],

        self::M_SV => [
            self::TP_ScM => 'Сервисный центр главная'
        ],

        self::M_Blc => [
            self::TP_Sb => 'Поиск по блогу'
        ],
        self::M_Txp => [
            self::TP_Mn => 'Главная',

            self::TP_Bg =>'Блог',
            self::TP_Dy =>'Доставка',
            self::TP_Py =>'Оплата',
            self::TP_Ho =>'Гарантия и возврат',
            self::TP_Fq =>'FAQ',
            self::TP_Or =>'Общие',
            self::TP_As =>'Акции',
            self::TP_Ct =>'Контакты',
            self::TP_At =>'О нас',
            self::TP_Cs =>'Сертификаты',
            self::TP_Oi =>'Правовая информация',
            self::TP_Ab =>'Все бренды',
            self::TP_Sc =>'Поиск по сайту',
            self::TP_Lm =>'Карта сайта',
       //     self::TP_Do =>'Доставка область',
       //     self::TP_Dc =>'Доставка город',
      //      self::TP_Ds =>'Доставка город-спутник',
            self::TP_Er =>'404',
            self::TP_SO => 'Распродажа'
        ],
        self::M_Bst => [
            self::TP_Bt => 'главная',
        ],
        self::M_Geo => [
            self::TP_Do =>'Доставка область',
                 self::TP_Dc =>'Доставка город',
        ],

        //
    ];



    public static  $arrTxtStatus = [ self::ST_OK => 'Включен', self::ST_NO =>'Выключен'];
    public static  $arrTxtSitemap = [ self::SM_OK => 'Включен', self::SM_NO =>'Выключен'];





    public  $def_module = 'textpage';

    public static function tableName()
    {
        return 'textpage';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['id', 'status' ,'sitemap'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['type_page','module'], 'string', 'max' => 70],
            [['module'], 'default', 'value'=>$this->def_module],
            [['sitemap'], 'default', 'value'=>1],
            [['status'], 'default', 'value'=> self::ST_OK ],
            [['text'], 'string' ],
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
            'status' => 'Статус',
            'type_page' => 'Тип страницы',
            'text' => 'Описание',
            'sitemap' => 'В карте сайта',
            'module' => 'Модуль'
        ];
    }


    //url
    public function getUrl_r(){

        return $this->hasOne( Url::class,
            ['identity' => 'id'])->andOnCondition(['controller' => 'textpage']);
    }


    public function getUrl_rr(){

        if ( isset( Textpage::$arrModules[ $this->type_page ] )){
            $this->def_module = Textpage::$arrModules[ $this->type_page ];
        }

        return $this->hasOne( Url::class, ['identity' => 'id'])
            ->andOnCondition(['controller' => $this->def_module, 'redirect'=>0]);


/*
        Url::$deph = 0;


        if ( isset( Textpage::$arrModules[ $this->type_page ] )){
            $this->def_module = Textpage::$arrModules[ $this->type_page ];
        }


        $url = Url::find()->where(['controller' =>$this->def_module,
            'action'=>$this->type_page,'identity'=>$this->id])->one();

        if ($url !== null){
            $url = Url::checkRedirect($url);
        }

        return $url;
*/

    }

    public function getUrl_m(){

        return $this->hasOne( Url::class,
            ['identity' => 'id'])->andOnCondition(['controller' => $this->module,'action' => $this->type_page]);
    }


    public function beforeSave($insert)
    {
       /* if (! $this->isNewRecord){
            $url = $this->url_rr;
            $url->setScenario('validHref');

            if ($url->public == Url::P_OK && $this->status == Textpage::ST_NO){
                $url->public = Url::P_NO;
                $url->update(false,['public']);
            }
            if ($url->public == Url::P_NO && $this->status ==  Textpage::ST_OK){
                $url->public = Url::P_OK;
                $url->update(false,['public']);
            }
        }*/
        Url::syncStatusEnt($this, Textpage::ST_OK,Textpage::ST_NO);
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function afterDelete()
    {
        foreach ( Url::findAll(['controller'=>$this->def_module,'action'=> $this->type_page ,'identity'=>$this->id]) as $item){
            $item->delete();
        }

        parent::afterDelete(); // TODO: Change the autogenerated stub
    }

    public function afterFind()
    {

        parent::afterFind(); // TODO: Change the autogenerated stub
    }

}
