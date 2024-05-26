<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\SalesReturnDetails;

/**
 * SalesReturnDetailsSearch represents the model behind the search form about `app\models\SalesReturnDetails`.
 */
class SalesReturnDetailsSearch extends SalesReturnDetails
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sales_details_id', 'sales_return_id', 'item_id', 'brand_id', 'size_id'], 'integer'],
            [['refund_amount', 'sales_amount', 'total_amount', 'quantity'], 'number'],
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
        $query = SalesReturnDetails::find();

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
            'sales_details_id' => $this->sales_details_id,
            'sales_return_id' => $this->sales_return_id,
            'item_id' => $this->item_id,
            'brand_id' => $this->brand_id,
            'size_id' => $this->size_id,
            'refund_amount' => $this->cost_amount,
            'sales_amount' => $this->sales_amount,
            'total_amount' => $this->total_amount,
        ]);


        return $dataProvider;
    }
}
