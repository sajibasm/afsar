<?php

namespace app\modules\asm\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ModulesActionSearch represents the model behind the search form about `app\models\ModulesAction`.
 */
class ModulesActionSearch extends ModulesAction
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'module'], 'integer'],
            [['code', 'name', 'active'], 'safe'],
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
        $query = ModulesAction::find();

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
            'module' => $this->module,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'active', $this->active]);

        return $dataProvider;
    }
}
