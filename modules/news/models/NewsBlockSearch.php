<?php

namespace app\modules\news\models;


use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CommentsSearch represents the model behind the search form about `common\models\Comments`.
 */
class NewsBlockSearch extends NewsBlock
{
    /**
     * @inheritdoc
     */



    public function rules()
    {
        return [

            [['news_id',  'ord', 'status'], 'integer'],
            [['desc'], 'string'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params,$news_id)
    {
        $query = NewsBlock::find()->where(['news_id'=>$news_id])->orderBy('ord');


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        return $dataProvider;
    }


}
