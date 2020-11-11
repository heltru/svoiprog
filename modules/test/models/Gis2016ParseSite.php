<?php

namespace app\modules\test\models;

use Yii;

/**
 * This is the model class for table "gis2016_parse_site".
 *
 * @property int $id
 * @property string $url
 * @property string $cat
 * @property string $subcat
 */
class Gis2016ParseSite extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gis2016_parse_site';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['url', 'cat', 'subcat'], 'required'],
            [['url', 'cat', 'subcat'], 'string', 'max' => 255],
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
            'cat' => 'Cat',
            'subcat' => 'Subcat',
        ];
    }
}
