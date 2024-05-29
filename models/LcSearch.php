<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Lc;

/**
 * LcSearch represents the model behind the search form about `app\models\Lc`.
 */
class LcSearch extends Lc
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lc_id', 'branch_id', 'user_id'], 'integer'],
            [['lc_name', 'lc_number', 'created_at', 'updated_at'], 'safe'],
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
        $query = Lc::find();

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
            'lc_id' => $this->lc_id,
            'branch_id' => $this->branch_id,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'lc_name', $this->lc_name])
            ->andFilterWhere(['like', 'lc_number', $this->lc_number]);

        return $dataProvider;
    }
}
