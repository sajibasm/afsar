<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ProductStockItemsDraftSearch represents the model behind the search form about `app\models\ProductStockItemsDraft`.
 */
class ProductStockItemsDraftSearch extends ProductStockItemsDraft
{
     /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_stock_items_draft_id', 'item_id', 'brand_id', 'size_id', 'alert_quantity'], 'integer'],
            [['cost_price', 'wholesale_price', 'retail_price',  'new_quantity', ], 'number'],
            //[['itemName', 'brandName', 'sizeName'], 'safe']
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
    public function searchByType()
    {
        $query = ProductStockItemsDraft::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['product_stock_items_draft_id'=>SORT_DESC]]
        ]);

        $query->andFilterWhere([
            'user_id' => Yii::$app->user->getId(),
            'type'=>$this->type,
            'source'=>$this->source
        ]);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        return $dataProvider;
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
        $query = ProductStockItemsDraft::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);



        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'product_stock_items_draft_id' => $this->product_stock_items_draft_id,
            'item_id' => $this->item_id,
            'brand_id' => $this->brand_id,
            'size_id' => $this->size_id,
            'cost_price' => $this->cost_price,
            'wholesale_price' => $this->wholesale_price,
            'retail_price' => $this->retail_price,
            'new_quantity' => $this->new_quantity,
            'alert_quantity' => $this->alert_quantity,
        ]);

        return $dataProvider;
    }
}
