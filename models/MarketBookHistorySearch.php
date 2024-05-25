<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\MarketBookHistory;

/**
 * MarketBookHistorySearch represents the model behind the search form about `app\models\MarketBookHistory`.
 */
class MarketBookHistorySearch extends MarketBookHistory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['market_sales_id', 'sales_id', 'client_id', 'item_id', 'brand_id', 'size_id', 'quantity', 'user_id'], 'integer'],
            [['unit', 'remarks', 'created_at', 'updated_at', 'status'], 'safe'],
            [['cost_amount', 'sales_amount', 'total_amount'], 'number'],
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
        $query = MarketBookHistory::find();

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
            'market_sales_id' => $this->market_sales_id,
            'sales_id' => $this->sales_id,
            'client_id' => $this->client_id,
            'item_id' => $this->item_id,
            'brand_id' => $this->brand_id,
            'size_id' => $this->size_id,
            'cost_amount' => $this->cost_amount,
            'sales_amount' => $this->sales_amount,
            'total_amount' => $this->total_amount,
            'quantity' => $this->quantity,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'unit', $this->unit])
            ->andFilterWhere(['like', 'remarks', $this->remarks])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
