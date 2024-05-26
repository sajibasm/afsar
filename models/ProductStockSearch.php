<?php

namespace app\models;

use app\components\Utility;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ProductStock;

/**
 * ProductStockSearch represents the model behind the search form about `app\models\ProductStock`.
 */
class ProductStockSearch extends ProductStock
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_stock_id', 'warehouse_id', 'lc_id', 'user_id'], 'integer'],
            [['created_at', 'updated_at', 'type'], 'safe'],
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
        $query = ProductStock::find();

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
            'product_stock_id' => $this->product_stock_id,
            'warehouse_id' => $this->warehouse_id,
            'lc_id' => $this->lc_id,
            'type' => $this->type,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->orderBy('product_stock_id DESC');

        return $dataProvider;
    }

    public function movement($params)
    {
        $query = ProductStock::find();
        $dataProvider = new ActiveDataProvider(['query' => $query,]);
        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['type'=>ProductStock::TYPE_MOVEMENT])
        ->orFilterWhere(['type'=>ProductStock::TYPE_TRANSFER])
            ->orderBy('product_stock_id DESC');

        return $dataProvider;
    }
}
