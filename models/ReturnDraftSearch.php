<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ReturnDraft;

/**
 * ReturnDraftSearch represents the model behind the search form about `app\models\ReturnDraft`.
 */
class ReturnDraftSearch extends ReturnDraft
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['return_draft_id', 'sales_id', 'item_id', 'brand_id', 'size_id', 'user_id'], 'integer'],
            [['refund_amount', 'total_amount', 'quantity'], 'number'],
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
        $query = ReturnDraft::find();

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
            'return_draft_id' => $this->return_draft_id,
            'sales_id' => $this->sales_id,
            'item_id' => $this->item_id,
            'brand_id' => $this->brand_id,
            'size_id' => $this->size_id,
            'refund_amount' => $this->refund_amount,
            'total_amount' => $this->total_amount,
            'quantity' => $this->quantity,
            'user_id' => $this->user_id,
        ]);

        return $dataProvider;
    }
}
