<?php

namespace app\models;

use app\components\DateTimeUtility;
use kartik\daterange\DateRangeBehavior;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\SalesDetails;

/**
 * SalesDetailsSearch represents the model behind the search form about `app\models\SalesDetails`.
 */
class SalesDetailsSearch extends SalesDetails
{

    public $created_at;
    public $datetime_start;
    public $datetime_end;
    public $brandTotal = 0;

    public $client_id;
    public $sortingBy;
    public $numberCustomer;
    public $created_to;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sales_details_id', 'sales_id', 'item_id', 'brand_id', 'size_id', 'client_id', 'sortingBy', 'numberCustomer', 'brandTotal'], 'integer'],
            [['unit', 'challan_unit', 'created_at', 'created_to'], 'safe'],
            [['cost_amount', 'sales_amount', 'total_amount', 'quantity', 'challan_quantity'], 'number'],

            [['created_at', 'datetime_start', 'datetime_end'], 'safe'],
            [['created_at'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => DateRangeBehavior::className(),
                'attribute' => 'created_at',
                'dateStartAttribute' => 'datetime_start',
                'dateEndAttribute' => 'datetime_end',
            ]
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
     * @param $params
     * @return ActiveDataProvider
     */
    public function details($params)
    {
        $query = SalesDetails::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => -1,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'sales_details_id' => $this->sales_details_id,
            'sales_id' => $this->sales_id,
            'item_id' => $this->item_id,
            'brand_id' => $this->brand_id,
            'size_id' => $this->size_id,
            'cost_amount' => $this->cost_amount,
            'sales_amount' => $this->sales_amount,
            'total_amount' => $this->total_amount,
            'quantity' => $this->quantity,
            'challan_quantity' => $this->challan_quantity,
        ]);

        $query->andFilterWhere(['like', 'unit', $this->unit])
            ->andFilterWhere(['like', 'challan_unit', $this->challan_unit]);

        return $dataProvider;
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
        $query = SalesDetails::find();

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
            'sales_details_id' => $this->sales_details_id,
            'sales_id' => $this->sales_id,
            'item_id' => $this->item_id,
            'brand_id' => $this->brand_id,
            'size_id' => $this->size_id,
            'cost_amount' => $this->cost_amount,
            'sales_amount' => $this->sales_amount,
            'total_amount' => $this->total_amount,
            'quantity' => $this->quantity,
            'challan_quantity' => $this->challan_quantity,
        ]);

        $query->andFilterWhere(['like', 'unit', $this->unit])
            ->andFilterWhere(['like', 'challan_unit', $this->challan_unit]);

        return $dataProvider;
    }

    public function brandWiseTopCustomer($params)
    {
        $query = SalesDetails::find()->innerJoinWith('sales', true);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            //'sort'=>['attributes'=>['sales.created_at DESC']]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }


        $query->andFilterWhere([
            'item_id' => $this->item_id,
            'brand_id' => $this->brand_id,
            'size_id' => $this->size_id,
            'quantity' => $this->quantity,
            'sales.client_id' => $this->client_id,
        ]);


        $query->andFilterWhere(['>=', 'created_at', $this->datetime_start])
            ->andFilterWhere(['<', 'created_at', $this->datetime_end]);

        $query->orderBy('quantity DESC');
        if(isset($this->sortingBy) && $this->sortingBy==1){
            $query->orderBy('sales.created_at DESC');
        }


        return $dataProvider;
    }

    public function potentialCustomerByBrand($params)
    {
        $query = SalesDetails::find()->join('LEFT OUTER JOIN','sales', 'sales.sales_id=sales_details.sales_id');
        $query->with(['item', 'brand', 'size']);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        $this->load($params);
        $query->andFilterWhere([
            'item_id' => $this->item_id,
            'brand_id' => $this->brand_id,
            'size_id' => $this->size_id,
            'sales.client_id' => $this->client_id,
        ]);

        $query->andFilterWhere(['>=', 'created_at', $this->datetime_start])
            ->andFilterWhere(['<', 'created_at', $this->datetime_end]);

        $query->select(['sales.client_id', 'sales.client_name', 'sales.created_at','sales.sales_id', 'item_id', 'brand_id', 'size_id', 'SUM(sales_details.total_amount) total_amount']);
        $query->groupBy('sales.client_id');
        $query->orderBy('total_amount DESC, sales.client_name ASC');
        return $dataProvider;
    }

    public function searchForReturn($params)
    {
        $query = SalesDetails::find()->from([SalesDetails::tableName().' AS salesDetails']);
        $query->joinWith(['salesReturnDetails']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        $query->andFilterWhere([
            'salesDetails.sales_id'=> $this->sales_id
        ]);

       $query->andFilterWhere(['!=', 'salesDetails.quantity', 'sales_return_details.quantity']);


        return $dataProvider;
    }

}
