<?php

namespace app\models;

use app\models\ProductStockItemsOutlet;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ProductStockItemsOutletSearch represents the model behind the search form about `app\models\ProductStockItemsOutlet`.
 */
class ProductStockItemsOutletSearch extends ProductStockItemsOutlet
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_stock_items_outlet_id', 'product_stock_outlet_id', 'item_id', 'brand_id', 'size_id'], 'integer'],
            [['cost_price', 'wholesale_price', 'retail_price', 'previous_quantity', 'new_quantity', 'total_quantity'], 'number'],
            [['status'], 'safe'],
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
        $query = ProductStockItemsOutlet::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'product_stock_items_outlet_id' => $this->product_stock_items_outlet_id,
            'product_stock_outlet_id' => $this->product_stock_outlet_id,
            'item_id' => $this->item_id,
            'brand_id' => $this->brand_id,
            'size_id' => $this->size_id,
            'cost_price' => $this->cost_price,
            'wholesale_price' => $this->wholesale_price,
            'retail_price' => $this->retail_price,
            'previous_quantity' => $this->previous_quantity,
            'new_quantity' => $this->new_quantity,
            'total_quantity' => $this->total_quantity,
        ]);

        $query->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }


    public function details()
    {
        $query = ProductStockItemsOutlet::find();

        $dataProvider = new ActiveDataProvider(['query' => $query,]);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $query->andFilterWhere(['product_stock_outlet_id' => $this->product_stock_outlet_id,]);
        return $dataProvider;
    }

}
