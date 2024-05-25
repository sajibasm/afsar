<?php

namespace app\models;

use app\components\Utility;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ClientPaymentDetails;

/**
 * ClientPaymentDetailsSearch represents the model behind the search form about `app\models\ClientPaymentDetails`.
 */
class ClientPaymentDetailsSearch extends ClientPaymentDetails
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_payment_details_id', 'sales_id', 'client_id'], 'integer'],
            [['paid_amount'], 'number'],
            [['payment_type', 'created_at', 'updated_at', 'payment_history_id'], 'safe'],
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
        $query = ClientPaymentDetails::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if(isset($params['id'])) {
            $this->payment_history_id = Utility::decrypt($params['id']);
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
//            'client_payment_details_id' => $this->client_payment_details_id,
//            'sales_id' => $this->sales_id,
//            'client_id' => $this->client_id,
            'payment_history_id' => $this->payment_history_id
//            'paid_amount' => $this->paid_amount,
//            'created_at' => $this->created_at,
//            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'payment_type', $this->payment_type]);

        return $dataProvider;
    }
}
