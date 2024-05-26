<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Challan;

/**
 * ChallanSearch represents the model behind the search form about `app\models\Challan`.
 */
class ChallanSearch extends Challan
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['challan_id', 'sales_id', 'client_id', 'transport_id', 'condition_id'], 'integer'],
            [['amount'], 'number'],
            [['transport_invoice_number', 'created_at', 'updated_at'], 'safe'],
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
        $query = Challan::find();

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
            'challan_id' => $this->challan_id,
            'sales_id' => $this->sales_id,
            'client_id' => $this->client_id,
            'amount' => $this->amount,
            'transport_id' => $this->transport_id,
            'condition_id' => $this->condition_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'transport_invoice_number', $this->transport_invoice_number]);

        return $dataProvider;
    }
}
