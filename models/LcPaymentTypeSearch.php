<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\LcPaymentType;

/**
 * LcPaymentTypeSearch represents the model behind the search form about `app\models\LcPaymentType`.
 */
class LcPaymentTypeSearch extends LcPaymentType
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lc_payment_type_id'], 'integer'],
            [['lc_payment_type_name', 'lc_payment_type_status'], 'safe'],
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
        $query = LcPaymentType::find();

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
            'lc_payment_type_id' => $this->lc_payment_type_id,
            'lc_payment_type_status' => $this->lc_payment_type_status,
        ]);

        $query->andFilterWhere(['like', 'lc_payment_type_name', $this->lc_payment_type_name]);

        return $dataProvider;
    }
}
