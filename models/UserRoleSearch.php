<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\UserRole;

/**
 * UserRoleSearch represents the model behind the search form about `app\models\UserRole`.
 */
class UserRoleSearch extends UserRole
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_role_id'], 'integer'],
            [['user_role_name', 'user_role_status'], 'safe'],
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
        $query = UserRole::find();

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
            'user_role_id' => $this->user_role_id,
        ]);

        $query->andFilterWhere(['like', 'user_role_name', $this->user_role_name])
            ->andFilterWhere(['like', 'user_role_status', $this->user_role_status]);

        return $dataProvider;
    }
}
