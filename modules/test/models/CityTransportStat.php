<?php

namespace app\modules\test\models;

use Yii;

/**
 * This is the model class for table "city_transport_stat".
 *
 * @property int $id
 * @property int $prediction
 * @property string $number
 * @property string $gn
 * @property string $date_cr
 */
class CityTransportStat extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'city_transport_stat';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date_cr'], 'required'],
            [['date_cr'], 'safe'],
            [['prediction'], 'integer'],

            [[  'number', 'gn'], 'string', 'max' => 45],
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
            'number' => 'Number',
            'gn' => 'Gn',
            'date_cr' => 'Date Cr',
        ];
    }
}
