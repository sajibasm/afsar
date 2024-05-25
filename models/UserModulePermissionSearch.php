<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\UserModulePermission;

/**
 * UserModulePermissionSearch represents the model behind the search form about `app\models\UserModulePermission`.
 */
class UserModulePermissionSearch extends UserModulePermission
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'module_id'], 'integer'],
            [['new', 'view', 'list', 'save', 'remove', 'added_at', 'added_by'], 'safe'],
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
        $query = UserModulePermission::find();

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
            'id' => $this->id,
            'user_id' => $this->user_id,
            'module_id' => $this->module_id,
            'added_at' => $this->added_at,
        ]);

        $query->andFilterWhere(['like', 'new', $this->new])
            ->andFilterWhere(['like', 'view', $this->view])
            ->andFilterWhere(['like', 'list', $this->list])
            ->andFilterWhere(['like', 'save', $this->save])
            ->andFilterWhere(['like', 'remove', $this->remove])
            ->andFilterWhere(['like', 'added_by', $this->added_by]);

        return $dataProvider;
    }
}
