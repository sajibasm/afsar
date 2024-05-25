<?php

namespace app\models;

use app\components\OutletUtility;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CustomerWithdraw;

/**
 * CustomerWithdrawSearch represents the model behind the search form about `app\models\CustomerWithdraw`.
 */
class CustomerWithdrawSearch extends CustomerWithdraw
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'payment_history_id', 'created_by', 'updated_by'], 'integer'],
            [['amount'], 'number'],
            [['remarks', 'status', 'created_at', 'updated_at', 'outletId'], 'safe'],
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
        $query = CustomerWithdraw::find();
        $query->where(['outletId' => array_keys(OutletUtility::getUserOutlet())]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if(!Yii::$app->asm->can('index-full')){
            $query->andFilterWhere([
                'user_id' => Yii::$app->user->id,
            ]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'payment_history_id' => $this->payment_history_id,
            'amount' => $this->amount,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'remarks', $this->remarks])
            ->andFilterWhere(['like', 'outletId', $this->outletId])
            ->andFilterWhere(['like', 'status', $this->status]);

        $query->orderBy('id DESC');

        return $dataProvider;
    }
}
