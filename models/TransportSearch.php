<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Transport;

/**
 * TransportSearch represents the model behind the search form about `app\models\Transport`.
 */
class TransportSearch extends Transport
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['transport_id'], 'integer'],
            [['transport_name', 'transport_address', 'transport_contact_person', 'transport_contact_number'], 'safe'],
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
        $query = Transport::find();

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
            'transport_id' => $this->transport_id,
        ]);

        $query->andFilterWhere(['like', 'transport_name', $this->transport_name])
            ->andFilterWhere(['like', 'transport_address', $this->transport_address])
            ->andFilterWhere(['like', 'transport_contact_person', $this->transport_contact_person])
            ->andFilterWhere(['like', 'transport_contact_number', $this->transport_contact_number]);

        return $dataProvider;
    }
}
