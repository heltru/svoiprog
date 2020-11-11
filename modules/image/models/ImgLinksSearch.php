<?php

namespace app\modules\image\models;



use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;


/**
 * ImgLinksSearch represents the model behind the search form about `common\models\ImgLinks`.
 */
class ImgLinksSearch extends ImgLinks
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_type', 'id_image'], 'integer'],
            [['type'], 'safe'],
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

    public function search($params,$id_type,$type)
    {


        /*  $query = ImgLinks::find()->leftJoin('img','img_links.id_image = img.id');
        //   $query->andWhere(['img.parent_id'=>0]);
           $query->andWhere(['img_links.type'=>$type]);
           $query->andWhere(['img_links.id_type'=>$id_type]);*/

        $query = Img::find()->leftJoin('img_links','img_links.id_image = img.id');
        $query->andWhere(['!=','img.parent_id',0]);
        $query->andWhere(['!=','img.fullsize',1]);

        $query->andWhere(['img_links.type'=>$type]);
        $query->andWhere(['img_links.id_type'=>$id_type]);



        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->orderBy('ord');

      //  echo $query->createCommand()->rawSql; exit;
        // grid filtering conditions

        return $dataProvider;
    }

    public function searchOld($params,$id_type,$type)
    {


        $query = ImgLinks::find()->leftJoin('img','img_links.id_image = img.id');
        $query->andWhere(['img.parent_id'=>0]);
        $query->andWhere(['img_links.type'=>$type]);
        $query->andWhere(['img_links.id_type'=>$id_type]);


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {

            return $dataProvider;

        }

        $query->orderBy('ord');
        // grid filtering conditions

        return $dataProvider;
    }
}
