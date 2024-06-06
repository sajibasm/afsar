<?php

namespace app\models;

use app\components\SystemSettings;
use app\components\CommonUtility;
use app\components\DateTimeUtility;
use app\components\OutletUtility;
use app\components\Utility;
use app\modules\asm\components\ASM;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Sales;

/**
 * SalesSearch represents the model behind the search form about `app\models\Sales`.
 */
class SalesSearch extends Sales
{

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


        if(!Yii::$app->asm->can('index-full')){
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
            if(!empty($this->created_at)){
                $query->andFilterWhere([
                    'BETWEEN',
                    'created_at',
                    DateTimeUtility::getStartTime(false, DateTimeUtility::getDate($this->created_at)),
                    DateTimeUtility::getEndTime(false, DateTimeUtility::getDate($this->created_to))
                ]);
            }
        }

        $query->with('user', 'paymentTypeModel');

        $query->orderBy('sales_id DESC');

        echo $query->createCommand()->rawSql;

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
