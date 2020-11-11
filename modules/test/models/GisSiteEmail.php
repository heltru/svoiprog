<?php

namespace app\modules\test\models;

use Yii;

/**
 * This is the model class for table "gis_site_email".
 *
 * @property int $id
 * @property string $email
 * @property int $site_id
 */
class GisSiteEmail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gis_site_email';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['site_id'], 'integer'],
            [['email'], 'string', 'max' => 255],
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
            'site_id' => 'Site ID',
        ];
    }
}
