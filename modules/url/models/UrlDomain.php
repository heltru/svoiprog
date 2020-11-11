<?php

namespace app\modules\url\models;

use app\modules\domain\models\Domain;
use Yii;

/**
 * This is the model class for table "url_domain".
 *
 * @property int $id
 * @property int $url_id
 * @property int $domain_id
 */
class UrlDomain extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'url_domain';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['url_id', 'domain_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'url_id' => 'Url ID',
            'domain_id' => 'Domain ID',
        ];
    }

    public function getDomain_r(){
        return $this->hasOne( Domain::class,['id'=> 'domain_id' ] );
    }

    public function getUrl_r(){
        return $this->hasOne( Url::class,['id'=> 'url_id' ] );
    }

    public function getUrl_rr(){
        return $this->hasOne( Url::class,['id'=> 'url_id' ] )->onCondition(['redirect'=>0]);
    }
}
