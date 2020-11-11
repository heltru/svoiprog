<?php

namespace app\modules\url\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\url\models\UrlRedirect;

/**
 * UrlRedirectSearch represents the model behind the search form about `app\modules\url\models\UrlRedirect`.
 */
class UrlRedirectSearch extends UrlRedirect
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['url_in', 'url_out'], 'safe'],
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
    public function search($params)
    {
        $query = UrlRedirect::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'url_in', $this->url_in])
            ->andFilterWhere(['like', 'url_out', $this->url_out]);

        return $dataProvider;
    }
}
