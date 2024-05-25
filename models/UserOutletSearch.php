<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\UserOutlet;

/**
 * UserOutletSearch represents the model behind the search form about `app\models\UserOutlet`.
 */
class UserOutletSearch extends UserOutlet
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userOutletId', 'userId', 'outletId', 'createdBy', 'updatedBy'], 'integer'],
            [['cretaedAt', 'updatedAt'], 'safe'],
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
        $query = UserOutlet::find();

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
            'userOutletId' => $this->userOutletId,
            'userId' => $this->userId,
            'outletId' => $this->outletId,
            'createdBy' => $this->createdBy,
            'updatedBy' => $this->updatedBy,
            'cretaedAt' => $this->cretaedAt,
            'updatedAt' => $this->updatedAt,
        ]);

        return $dataProvider;
    }
}
