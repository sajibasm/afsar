<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\SalaryHistory;

/**
 * SalaryHistorySearch represents the model behind the search form about `app\models\SalaryHistory`.
 */
class SalaryHistorySearch extends SalaryHistory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'employee_id', 'month', 'year', 'user_id'], 'integer'],
            [['withdraw_amount', 'remaining_salary'], 'number'],
            [['remarks', 'created_at', 'updated_at'], 'safe'],
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
        $query = SalaryHistory::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $permissions = Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->getId());
        if (!isset($permissions['ALL_USER_DATA'])) {
            $query->andFilterWhere([
                'user_id' => Yii::$app->user->id,
            ]);
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'withdraw_amount' => $this->withdraw_amount,
            'remaining_salary' => $this->remaining_salary,
            'month' => $this->month,
            'year' => $this->year
        ]);

        $query->andFilterWhere(['like', 'remarks', $this->remarks]);

        $query->orderBy('id DESC');

        return $dataProvider;
    }

    public function salary($params)
    {
        $query = SalaryHistory::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $permissions = Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->getId());
        if (!isset($permissions['ALL_USER_DATA'])) {
            $query->andFilterWhere([
                'user_id' => Yii::$app->user->id,
            ]);
        }

        $query->andFilterWhere([
            'employee_id' => $this->employee_id,
            'withdraw_amount' => $this->withdraw_amount,
            'remaining_salary' => $this->remaining_salary,
            'month' => $this->month,
            'year' => $this->year
        ]);

        $query->andFilterWhere(['like', 'remarks', $this->remarks]);

        $query->orderBy('id DESC');

        return $dataProvider;
    }

}
