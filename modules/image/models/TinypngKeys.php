<?php

namespace app\modules\image\models;

use Yii;

/**
 * This is the model class for table "tinypng_keys".
 *
 * @property int $id
 * @property string $key
 * @property int $count_uses
 */
class TinypngKeys extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tinypng_keys';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['count_uses'], 'integer'],
            [['key'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'count_uses' => 'Count Uses',
        ];
    }
}
