<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ProductItemsPrice;

/**
 * ProductItemsPriceSearch represents the model behind the search form about `app\models\ProductItemsPrice`.
 */
class ProductItemsPriceSearch extends ProductItemsPrice
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_stock_items_id', 'item_id', 'brand_id', 'size_id'], 'integer'],
            [['cost_price', 'wholesale_price', 'retail_price', 'quantity', 'total_quantity', 'alert_quantity'], 'number'],
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
        $query = ProductItemsPrice::find();

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
            'product_stock_items_id' => $this->product_stock_items_id,
            'item_id' => $this->item_id,
            'brand_id' => $this->brand_id,
            'size_id' => $this->size_id,
            'cost_price' => $this->cost_price,
            'wholesale_price' => $this->wholesale_price,
            'retail_price' => $this->retail_price,
            'quantity' => $this->quantity,
            'total_quantity' => $this->total_quantity,
            'alert_quantity' => $this->alert_quantity,
        ]);

        return $dataProvider;
    }
}
