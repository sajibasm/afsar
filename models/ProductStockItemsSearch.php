<?php

namespace app\models;

use app\components\CommonUtility;
use app\components\DateTimeUtility;
use app\models\ProductStockItems;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;


/**
 * ProductStockItemsSearch represents the model behind the search form about `app\models\ProductStockItems`.
 */
class ProductStockItemsSearch extends ProductStockItems
{

    public $client_id = null;
    public $sortingBy = null;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_stock_items_id', 'product_stock_id', 'item_id', 'brand_id', 'size_id'], 'integer'],
            [['cost_price', 'wholesale_price', 'retail_price', 'previous_quantity', 'new_quantity', 'total_quantity'], 'number'],
            [['lc', 'warehouse', 'supplier'], 'integer'],
            [['created_at', 'created_to', 'type'], 'safe'],
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

    public function details($params)
    {
        $query = ProductStockItems::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'product_stock_id' => $this->product_stock_id,
        ]);

        return $dataProvider;
    }


    public function view()
    {
        $query = ProductStockItems::find();
        $query->andFilterWhere(['product_stock_id' => $this->product_stock_id,]);
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 200,
            ]
        ]);

    }

    public function search($params, $isToday)
    {
        $query = ProductStockItems::find();

        $query->joinWith(['productStock']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize'],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'product_stock_items_id' => $this->product_stock_items_id,
            'product_stock_id' => $this->product_stock_id,
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


        $query->andFilterWhere(['product_stock.lc_id' => $this->lc])
            ->andFilterWhere(['product_stock.warehouse_id' => $this->warehouse])
            ->andFilterWhere(['product_stock.product_stock_id' => $this->product_stock_id])
            ->andFilterWhere(['product_stock.buyer_id' => $this->supplier]);


        if (empty($this->type)) {
            $query->andFilterWhere(['in', 'product_stock.type', [ProductStock::TYPE_LOCAL, ProductStock::TYPE_IMPORT]]);
        } else {
            $query->andFilterWhere(['product_stock.type' => $this->type]);
        }

        if (!empty($this->created_at)) {
            $query->andFilterWhere(['>=', 'product_stock.created_at', DateTimeUtility::getDate(DateTimeUtility::getStartTime(false, $this->created_at))]);
            $query->andFilterWhere(['<=', 'product_stock.created_at', DateTimeUtility::getDate(DateTimeUtility::getEndTime(false, $this->created_to))]);
        }

        $query->orderBy([
            'product_stock.product_stock_id' => SORT_DESC,
        ]);

        //CommonUtility::debug($query->createCommand()->getRawSql());
        return $dataProvider;
    }

    public function searchReport($params, $isToday)
    {
        $query = ProductStockItems::find();

        $query->joinWith(['productStock']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize'],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }


        $query->andFilterWhere([
            'product_stock_id' => $this->product_stock_id,
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

        if (empty($this->type)) {
            $query->andFilterWhere(['in', 'product_stock.type', [ProductStock::TYPE_MOVEMENT, ProductStock::TYPE_TRANSFER]]);
        } else {
            $query->andFilterWhere(['product_stock.type' => $this->type]);
        }

        $query->andFilterWhere(['product_stock.product_stock_id' => $this->product_stock_id]);

        if (!empty($this->created_at)) {
            $query->andFilterWhere(['>=', 'product_stock.created_at', DateTimeUtility::getDate(DateTimeUtility::getStartTime(false, $this->created_at))]);
            $query->andFilterWhere(['<=', 'product_stock.created_at', DateTimeUtility::getDate(DateTimeUtility::getEndTime(false, $this->created_to))]);
        }

        $query->orderBy([
            'product_stock.product_stock_id' => SORT_DESC,
        ]);

        return $dataProvider;
    }

    public function movement($params, $isToday)
    {
        $query = ProductStockItems::find();
        $query->joinWith(['productStock']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => Yii::$app->params['pageSize']],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'product_stock_items_id' => $this->product_stock_items_id,
            //'product_stock_id' => $this->product_stock_id,
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

        $query->andFilterWhere(['product_stock.type' => ProductStock::TYPE_MOVEMENT])
            ->orFilterWhere(['product_stock.type' => ProductStock::TYPE_TRANSFER])
            ->andFilterWhere(['product_stock.product_stock_id' => $this->product_stock_id]);


//        if(!empty($this->created_at)){
//            $query->andFilterWhere(['>=', 'product_stock.created_at', DateTimeUtility::getDate(DateTimeUtility::getStartTime(false, $this->created_at))]);
//            $query->andFilterWhere(['<=', 'product_stock.created_at', DateTimeUtility::getDate(DateTimeUtility::getEndTime(false, $this->created_to))]);
//        }

        $query->orderBy([
            'product_stock_id' => SORT_DESC,
        ]);

        //CommonUtility::debug($query);

        return $dataProvider;
    }

}
