<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\SalesDraft;

/**
 * SalesDraftSearch represents the model behind the search form about `app\models\SalesDraft`.
 */
class SalesDraftSearch extends SalesDraft
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sales_details_id', 'sales_id', 'item_id', 'brand_id', 'size_id', 'user_id', 'outletId'], 'integer'],
            [['cost_amount', 'sales_amount', 'total_amount', 'quantity', 'challan_quantity'], 'number'],
            [['challan_unit', 'type'], 'safe'],
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
        $query = SalesDraft::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=>['defaultOrder'=>['sales_details_id'=>SORT_DESC]],
            'pagination' => [
                'pageSize' =>100,
            ]
        ]);

        //'sort'=> ['defaultOrder' => ['item'=>SORT_ASC]]

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'sales_details_id' => $this->sales_details_id,
            'user_id' => $this->user_id,
            'outletId' => $this->outletId,
        ]);

        $query->andFilterWhere(['like', 'type', $this->type]);

        return $dataProvider;
    }

    public function searchUpdateRemoved($params)
    {
        $query = SalesDraft::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'sales_id' => $this->sales_id,
            'user_id' => $this->user_id,
            'outletId' => $this->outletId,
        ]);

        $query->andFilterWhere(['=', 'type', SalesDraft::TYPE_UPDATE_DELETED]);
        return $dataProvider;
    }

    public function searchUpdate($params)
    {
        $query = SalesDraft::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'sales_id' => $this->sales_id,
            'user_id' => $this->user_id,
            'outletId' => $this->outletId,
        ]);

        $query->andFilterWhere([ '=', 'type', SalesDraft::TYPE_UPDATE ]);
        $query->orFilterWhere([ '=', 'type', SalesDraft::TYPE_UPDATE_ADDED]);
        return $dataProvider;
    }

}

