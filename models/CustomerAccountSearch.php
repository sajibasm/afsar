<?php

namespace app\models;

use app\components\CommonUtility;
use app\components\CustomerUtility;
use app\components\DateTimeUtility;
use app\components\Utility;
use kartik\daterange\DateRangeBehavior;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CustomerAccount;

/**
 * CustomerAccountSearch represents the model behind the search form about `app\models\CustomerAccount`.
 */
class CustomerAccountSearch extends CustomerAccount
{
    public $created_at;
    public $datetime_start;
    public $datetime_end;
    const DURATION_DAYS= 'days';
    const DURATION_MONTH = 'month';
    const DURATION_YEAR = 'year';

    public $duration;
    public $durationValues;

    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'sales_id', 'client_id', 'totalDues', 'durationValues'], 'integer'],
            [['memo_id', 'type', 'date', 'fromDate', 'toDate', 'duration'], 'safe'],
            [['debit', 'credit', 'balance'], 'number'],

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


    public function searchDues($params)
    {


        $query = CustomerAccount::find();

        $this->load($params);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);


        if(!empty($this->duration) && !empty($this->durationValues)){
            if($this->duration==self::DURATION_DAYS){
                $previousDate = date("Y-m-d", strtotime("-{$this->durationValues} days"));
            }else if($this->duration==self::DURATION_MONTH){
                $previousDate = date("Y-m-01", strtotime("-{$this->durationValues} months"));
            }else{
                $previousDate = date("Y-m-01", strtotime("-{$this->durationValues} years"));
            }
            $query->andFilterWhere(['IN', 'client_id', CustomerUtility::getCustomerIdLastPaymentDate($previousDate)]);
        }else{
            if(empty($this->client_id)){
                $query->andFilterWhere(['IN', 'client_id', CustomerUtility::getCustomerHasDue(true)]);
            }else{
                $query->andFilterWhere(['IN', 'client_id', $this->client_id]);
            }
        }

        $query->with('clientPaymentHistory', 'client');
        $query->select('SUM(debit) - SUM(credit) as balance, client_id');
        $query->groupBy('client_id');
        $query->orderBy('balance DESC');
        //echo $query->createCommand()->getRawSql();
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
        $query = CustomerAccount::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => -1,
            ],
        ]);

        $this->load($params);

        if(!empty(Yii::$app->request->getQueryParam('_pjax')) || empty(Yii::$app->request->queryParams)) {
            $query->where('0=1');
        }

        if($this->sales_id==null && $this->client_id==null){
            $query->where('0=1');
        }


        $query->andFilterWhere(['>=', 'date', $this->datetime_start])
            ->andFilterWhere(['<', 'date', $this->datetime_end]);

        $query->andFilterWhere([
            'sales_id' => $this->sales_id,
            'client_id' => $this->client_id,
        ]);

        $query->andFilterWhere(['like', 'type', $this->type]);

        $query->orderBy('id ASC');

        return $dataProvider;
    }

    public function searchHasBalance($params)
    {
        $query = CustomerAccount::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => -1,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'client_id' => $this->client_id,
            'sales_id'=>CustomerUtility::getInvoiceListByCustomer($this->client_id)
        ]);

        $query->orderBy('sales_id');


        return $dataProvider;
    }
}
