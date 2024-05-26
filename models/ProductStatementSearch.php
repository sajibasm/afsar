<?php

namespace app\models;

use app\components\DateTimeUtility;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ProductStatement;

/**
 * ProductStatementSearch represents the model behind the search form about `app\models\ProductStatement`.
 */
class ProductStatementSearch extends ProductStatement
{
    public $created_to;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_statement_id', 'item_id', 'brand_id', 'size_id', 'reference_id', 'user_id'], 'integer'],
            [['created_to'], 'string'],
            [['quantity'], 'number'],
            [['type', 'remarks', 'created_at', 'updated_at', 'created_to'], 'safe'],
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
    public function search($params, $isToday = false)
    {
        $query = ProductStatement::find();

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
            'product_statement_id' => $this->product_statement_id,
            'item_id' => $this->item_id,
            'brand_id' => $this->brand_id,
            'size_id' => $this->size_id,
            'quantity' => $this->quantity,
            'reference_id' => $this->reference_id,
        ]);

        $query->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'remarks', $this->remarks]);



        if($isToday){
            $query->andFilterWhere(['>=', 'created_at', DateTimeUtility::getTodayStartTime()]);
            $query->andFilterWhere(['<=', 'created_at', DateTimeUtility::getTodayEndTime()]);
        }else{
            if(!empty($this->created_at)){
                $query->andFilterWhere(['>=', 'created_at', DateTimeUtility::getDate(DateTimeUtility::getStartTime(false, $this->created_at))]);
                $query->andFilterWhere(['<=', 'created_at', DateTimeUtility::getDate(DateTimeUtility::getEndTime(false, $this->created_to))]);
            }
        }

        $query->orderBy('product_statement_id DESC');

        return $dataProvider;
    }
}
