<?php

namespace app\modules\test\models;

use Yii;

/**
 * This is the model class for table "city_transport".
 *
 * @property int $id
 * @property int $prediction
 * @property int $minutes
 * @property string $gn
 * @property string $number
 * @property string $date_cr
 */
class CityTransport extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'city_transport';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['prediction', 'gn', 'number', 'date_cr'], 'required'],
            [['prediction','minutes'], 'integer'],
            [['date_cr'], 'safe'],
            [['gn', 'number'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'prediction' => 'Prediction',
            'gn' => 'Gn',
            'number' => 'Number',
            'date_cr' => 'Date Cr',
        ];
    }
}
