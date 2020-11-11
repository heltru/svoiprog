<?php

namespace app\modules\test\models;

use Yii;

/**
 * This is the model class for table "city_transport_check".
 *
 * @property int $id
 * @property string $number
 * @property string $date
 * @property string $gn
 * @property int $route_id
 * @property int $area
 * @property float $lat
 * @property float $long
 */
class CityTransportCheck extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'city_transport_check';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date'], 'required'],
            [['date'], 'safe'],
            [['route_id','area'], 'integer'],
            [['lat', 'long'], 'number'],
            [['number', 'gn'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'number' => 'Number',
            'date' => 'Date',
            'gn' => 'Gn',
            'route_id' => 'Route ID',
            'lat' => 'Lat',
            'long' => 'Long',
        ];
    }
}
