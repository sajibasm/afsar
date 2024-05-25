<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ExpenseType;

/**
 * ExpenseTypeSearch represents the model behind the search form about `app\models\ExpenseType`.
 */
class ExpenseTypeSearch extends ExpenseType
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['expense_type_id', ], 'integer'],
            [['expense_type_name', 'expense_type_status', 'expense_type_mode'], 'safe'],
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
        $query = ExpenseType::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['expense_type_name'=>SORT_ASC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'expense_type_mode' => $this->expense_type_mode,
        ]);

        $query->andFilterWhere(['like', 'expense_type_name', $this->expense_type_name]);
        $query->andFilterWhere(['like', 'expense_type_status', $this->expense_type_status]);

        return $dataProvider;
    }
}
