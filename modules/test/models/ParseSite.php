<?php

namespace app\modules\test\models;

use Yii;

/**
 * This is the model class for table "parse_site".
 *
 * @property int $id
 * @property string $url
 * @property string $source
 */
class ParseSite extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'parse_site';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['url'], 'string', 'max' => 512],
            [['source'], 'string', 'max' => 45],
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
            'source' => 'Source',
        ];
    }
}
