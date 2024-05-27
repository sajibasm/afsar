<?php

namespace app\models;

use app\components\SystemSettings;

use app\components\DateTimeUtility;
use app\components\OutletUtility;
use kartik\daterange\DateRangeBehavior;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use mdm\admin\components\Helper;

/**
 * SalesSearch represents the model behind the search form about `app\models\Sales`.
 */
class SalesSearch extends Sales
{

    public $created_at;
    public $datetime_start;
    public $datetime_end;
    const PAYMENT_PARTIAL = 'Partial';
    const PAYMENT_CREDIT = 'Credit';
    const PAYMENT_PAID= 'Paid';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sales_id', 'client_id', 'client_type', 'user_id', 'payment_type', 'transport_id', 'outletId'], 'integer'],
            [['client_name', 'contact_number', 'remarks', 'created_at', 'updated_at', 'created_to', 'invoiceType'], 'safe'],
            [['paid_amount', 'due_amount', 'discount_amount', 'total_amount', 'received_amount'], 'number'],

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
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @param bool $isToday
     * @return ActiveDataProvider
     */
    public function search($params, $isToday = false)
    {

        $query = Sales::find();
        $query->where(['outletId' => array_keys(OutletUtility::getUserOutlet())]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => SystemSettings::getPerPageRecords(),
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        if(!empty($this->invoiceType)){
            if($this->invoiceType==self::PAYMENT_CREDIT){
                $query->andFilterWhere([
                    'received_amount'=>0,
                ]);
            }else if($this->invoiceType==self::PAYMENT_PAID){
                $query->andFilterWhere([
                    'due_amount'=>0,
                ]);
            }else{
                $query->andFilterWhere(['>', 'due_amount', 0]);
                $query->andFilterWhere(['>', 'received_amount', 0]);
            }
        }


        if(Helper::checkRoute('full-index')){
            $query->andFilterWhere([
                'user_id' => Yii::$app->user->id,
            ]);
        }

        $query->andFilterWhere([
            'sales_id' => $this->sales_id,
            'outletId' => $this->outletId,
            'client_id' => $this->client_id,
            'client_type' => $this->client_type,
            'transport_id' => $this->transport_id,
            'tracking_number' => $this->tracking_number,
            'contact_number' => $this->contact_number,
            'payment_type' => $this->payment_type,
        ]);

        $query->andFilterWhere(['like', 'client_name', $this->client_name]);

        if($isToday){
            $query->andFilterWhere(['>=', 'created_at', DateTimeUtility::getTodayStartTime()]);
            $query->andFilterWhere(['<=', 'created_at', DateTimeUtility::getTodayEndTime()]);
        }else{
            $query->andFilterWhere(['>=', 'created_at', $this->datetime_start])
                ->andFilterWhere(['<', 'created_at', $this->datetime_end]);
        }

        $query->with('user', 'paymentTypeModel');

        $query->orderBy('sales_id DESC');

        return $dataProvider;
    }

    public function topProductSearch($params)
    {
        $query = Sales::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize'],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'sales_id' => $this->sales_id,
            'client_id' => $this->client_id,
            'client_type' => $this->client_type,
//            'user_id' => $this->user_id,
//            'paid_amount' => $this->paid_amount,
//            'due_amount' => $this->due_amount,
//            'discount_amount' => $this->discount_amount,
//            'total_amount' => $this->total_amount,
        ]);

        $query->andFilterWhere(['like', 'client_name', $this->client_name]);
        //->andFilterWhere(['like', 'contact_number', $this->contact_number]);
        //->andFilterWhere(['like', 'remarks', $this->remarks]);


        if(!empty($this->created_at)){
            $query->andFilterWhere(['>=', 'created_at', DateTimeUtility::getDate(DateTimeUtility::getStartTime(false, $this->created_at))]);
            $query->andFilterWhere(['<=', 'created_at', DateTimeUtility::getDate(DateTimeUtility::getEndTime(false, $this->created_to))]);
        }


        $query->with('user', 'paymentTypeModel');
        $query->orderBy('sales_id DESC');

        return $dataProvider;
    }

}
