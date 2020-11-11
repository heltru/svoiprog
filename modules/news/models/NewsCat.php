<?php

namespace app\modules\news\models;

use Yii;

/**
 * This is the model class for table "news_cat".
 *
 * @property int $id
 * @property string $name
 * @property int $parent_id
 */
class NewsCat extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'news_cat';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent_id'], 'integer'],
            [['parent_id'], 'default','value'=>0],
            [['name'], 'string', 'max' => 255],
            [['li_id_tag'], 'string', 'max' => 45],
            [['seo_menu_item'], 'integer'],
            [['seo_menu_item'], 'default','value'=>0],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'parent_id' => 'Parent ID',
        ];
    }

    public function getSeoMenuItem_r(){
        return $this->hasOne( NewsCat::class, ['id' => 'seo_menu_item']);
    }

    public function getParents_r()
    {
        return $this->hasMany( NewsCat::class, ['parent_id' => 'id'])->orderBy(['ord'=>SORT_ASC]);
    }

    public function getParentCat_r(){
        return $this->hasOne(  NewsCat::class, ['id' => 'parent_id']);
    }

}
