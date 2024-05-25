<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Client;

/**
 * ClientSearch represents the model behind the search form about `app\models\Client`.
 */
class ClientSearch extends Client
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id', 'client_city'], 'integer'],
            [['client_name', 'client_address1', 'client_address2', 'client_contact_number', 'client_contact_person', 'client_contact_person_number', 'client_type'], 'safe'],
            [['client_balance'], 'number'],
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
        $query = Client::find();

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
            'client_id' => $this->client_id,
            'client_city' => $this->client_city,
            'client_balance' => $this->client_balance,
        ]);

        $query->andFilterWhere(['like', 'client_name', $this->client_name])
            ->andFilterWhere(['like', 'client_address1', $this->client_address1])
            ->andFilterWhere(['like', 'client_address2', $this->client_address2])
            ->andFilterWhere(['like', 'client_contact_number', $this->client_contact_number])
            ->andFilterWhere(['like', 'client_contact_person', $this->client_contact_person])
            ->andFilterWhere(['like', 'client_contact_person_number', $this->client_contact_person_number])
            ->andFilterWhere(['like', 'client_type', $this->client_type]);

        return $dataProvider;
    }
}
