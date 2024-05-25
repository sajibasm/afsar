<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Brand;

/**
 * BrandSearch represents the model behind the search form about `app\models\Brand`.
 */
class BrandSearch extends Brand
{
    public $item;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['brand_id', 'item_id'], 'integer'],
            [['brand_name', 'brand_status'], 'safe'],
            [['item'], 'safe']

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
        $query = Brand::find();

        $query->joinWith(['item']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['item'=>SORT_ASC]]
        ]);

        $dataProvider->sort->attributes['item'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['item.item_name' => SORT_ASC],
            'desc' => ['item.item_name' => SORT_DESC],
        ];


        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'brand_status' => $this->brand_status,
        ]);

        $query->andFilterWhere(['like', 'brand_name', $this->brand_name])
            ->andFilterWhere(['like', 'item.item_name', $this->item])
            ->andFilterWhere(['like', 'brand_status', $this->brand_status]);

        return $dataProvider;
    }
}
