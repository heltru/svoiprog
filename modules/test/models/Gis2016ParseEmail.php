<?php

namespace app\modules\test\models;

use Yii;

/**
 * This is the model class for table "gis2016_parse_email".
 *
 * @property int $id
 * @property string $email
 * @property string $cat
 * @property string $subcat
 */
class Gis2016ParseEmail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gis2016_parse_email';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'cat', 'subcat'], 'required'],
            [['email', 'cat', 'subcat'], 'string', 'max' => 255],
            [['email'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'cat' => 'Cat',
            'subcat' => 'Subcat',
        ];
    }
}
