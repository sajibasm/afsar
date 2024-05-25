<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Outlet;

/**
 * OutletSearch represents the model behind the search form about `app\models\Outlet`.
 */
class OutletSearch extends Outlet
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['outletId'], 'integer'],
            [['outletCode', 'name', 'address1', 'address2', 'logo', 'logoWaterMark', 'contactNumber', 'email', 'status'], 'safe'],
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
        $query = Outlet::find();

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
            'outletId' => $this->outletId,
        ]);

        $query->andFilterWhere(['like', 'outletCode', $this->outletCode])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'address1', $this->address1])
            ->andFilterWhere(['like', 'address2', $this->address2])
            ->andFilterWhere(['like', 'logo', $this->logo])
            ->andFilterWhere(['like', 'logoWaterMark', $this->logoWaterMark])
            ->andFilterWhere(['like', 'contactNumber', $this->contactNumber])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
