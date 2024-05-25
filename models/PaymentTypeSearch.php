<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PaymentType;

/**
 * PaymentTypeSearch represents the model behind the search form about `app\models\PaymentType`.
 */
class PaymentTypeSearch extends PaymentType
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['payment_type_id'], 'integer'],
            [['payment_type_name', 'type'], 'safe'],
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
        $query = PaymentType::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['payment_type_name'=>SORT_ASC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'type' => 'Cash',
        ]);

        $query->orFilterWhere([
            'type' => 'Bank',
        ]);


        $query->andFilterWhere(['like', 'payment_type_name', $this->payment_type_name])
        ->andFilterWhere(['like', 'type', $this->type]);

        return $dataProvider;
    }
}
