<?php

namespace app\models;

use app\components\StoreUtility;
use app\models\ProductStockOutlet;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;


/**
 * ProductStockOutletSearch represents the model behind the search form about `app\models\ProductStockOutlet`.
 */
class ProductStockOutletSearch extends ProductStockOutlet
{
    public $datetime_start;
    public $datetime_end;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_stock_outlet_id'], 'integer'],
            [['transferFrom', 'transferBy', 'receivedBy', 'product_stock_outlet_code', 'invoice', 'note', 'type', 'remarks', 'params', 'createdAt', 'updatedAt', 'status', 'transferOutlet', 'receivedOutlet',], 'safe'],

            [['createdAt', 'datetime_start', 'datetime_end'], 'safe'],
            [['createdAt'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
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

        // Join with aliases to avoid table name conflicts
        $query->joinWith([
            'transferOutletDetail', // joins `outlet` table
            'transferByUser' => function ($q) { $q->alias('transferUser'); },
            'receivedByUser' => function ($q) { $q->alias('receivedUser'); },
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // Direct filters
        if (!empty($this->createdAt) && strpos($this->createdAt, ' - ') !== false) {
            list($startDate, $endDate) = explode(' - ', $this->createdAt);
            $startDate .= ' 00:00:00';
            $endDate .= ' 23:59:59';

            $query->andFilterWhere(['between', 'product_stock_outlet.createdAt', $startDate, $endDate]);
        } else {
            $query->andFilterWhere(['product_stock_outlet.createdAt' => $this->createdAt]);
        }

        // LIKE filters with full table prefixes or aliases
        $query->andFilterWhere(['like', 'product_stock_outlet.product_stock_outlet_code', $this->product_stock_outlet_code])
            ->andFilterWhere(['like', 'product_stock_outlet.invoice', $this->invoice])
            ->andFilterWhere(['like', 'product_stock_outlet.note', $this->note])
            ->andFilterWhere(['like', 'product_stock_outlet.type', $this->type])
            ->andFilterWhere(['like', 'product_stock_outlet.remarks', $this->remarks])
            ->andFilterWhere(['like', 'product_stock_outlet.params', $this->params])
            ->andFilterWhere(['like', 'transferOutlet.name', $this->transferOutlet])
            ->andFilterWhere(['like', 'receivedOutlet.name', $this->receivedOutlet])
            ->andFilterWhere(['like', 'transferUser.user_id', $this->transferBy])
            ->andFilterWhere(['like', 'receivedUser.user_id', $this->receivedBy])
            ->andFilterWhere(['like', 'product_stock_outlet.status', $this->status]);

        // Optional: Filter for specific user outlet visibility
        if (StoreUtility::countUserStores() === 1) {
            $outId = StoreUtility::getDefaultStoreByUser();
            $query->andFilterWhere(['product_stock_outlet.receivedOutlet' => $outId])
                ->orFilterWhere(['product_stock_outlet.transferOutlet' => $outId]);
        }

        // Sorting
        $query->orderBy('product_stock_outlet.product_stock_outlet_id DESC');

        // Load related data
        $query->with([
            'receivedByUser',
            'transferOutletDetail',
            'receivedOutletDetail',
            'transferByUser',
        ]);

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
