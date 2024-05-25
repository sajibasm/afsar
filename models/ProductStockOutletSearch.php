<?php

namespace app\models;

use app\components\OutletUtility;
use app\models\ProductStockOutlet;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;


/**
 * ProductStockOutletSearch represents the model behind the search form about `app\models\ProductStockOutlet`.
 */
class ProductStockOutletSearch extends ProductStockOutlet
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_stock_outlet_id'], 'integer'],
            [['transferFrom', 'transferBy', 'receivedBy', 'product_stock_outlet_code', 'invoice', 'note', 'type', 'remarks', 'params', 'createdAt', 'updatedAt', 'status', 'transferOutlet', 'receivedOutlet',], 'safe'],
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
        $query = ProductStockOutlet::find();
        $query->joinWith(['transferOutletDetail', 'transferByUser']);

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
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ]);


        $query->andFilterWhere(['like', 'product_stock_outlet_code', $this->product_stock_outlet_code])
            ->andFilterWhere(['like', 'invoice', $this->invoice])
            ->andFilterWhere(['like', 'note', $this->note])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'remarks', $this->remarks])
            ->andFilterWhere(['like', 'params', $this->params])
            ->andFilterWhere(['like', 'outlet.name', $this->transferOutlet])
            ->andFilterWhere(['like', 'outlet.name', $this->receivedOutlet])
            ->andFilterWhere(['like', 'user.username', $this->transferBy])
            ->andFilterWhere(['like', 'status', $this->status]);


        if(OutletUtility::numberOfOutletByUser()===1){
            $outId  = OutletUtility::defaultOutletByUser();
            $query->andFilterWhere(['receivedOutlet'=>$outId]);
            $query->orFilterWhere(['transferOutlet'=>$outId]);
        }

        $query->orderBy('product_stock_outlet_id DESC');
        $query->with(['receivedByUser', 'transferOutletDetail', 'receivedOutletDetail', 'transferByUser', 'receivedByUser']);
        return $dataProvider;
    }

    public function movement($params)
    {
        $query = ProductStockOutlet::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {return $dataProvider;}

        $query->andFilterWhere(['type'=>'Movement']);

        return $dataProvider;
    }

    public function details($params)
    {
        $query = ProductStockOutlet::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['product_stock_outlet_id' => $this->product_stock_outlet_id,]);

        return $dataProvider;
    }

}
