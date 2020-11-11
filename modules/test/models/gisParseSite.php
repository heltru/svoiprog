<?php

namespace app\modules\test\models;

use Yii;

/**
 * This is the model class for table "2gis_parse_site".
 *
 * @property int $id
 * @property string $url
 * @property string $email_id
 * @property string $cat
 * @property string $subcat
 */
class gisParseSite extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gis_parse_site';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subcat'], 'required'],
            [['url', 'email_id', 'cat', 'subcat'], 'string', 'max' => 255],
            [['url'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'url' => 'Url',
            'email_id' => 'Email ID',
            'cat' => 'Cat',
            'subcat' => 'Sub Cat',
        ];
    }
}
