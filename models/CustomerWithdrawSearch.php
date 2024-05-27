<?php

namespace app\models;

use app\components\OutletUtility;
use kartik\daterange\DateRangeBehavior;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CustomerWithdraw;

/**
 * CustomerWithdrawSearch represents the model behind the search form about `app\models\CustomerWithdraw`.
 */
class CustomerWithdrawSearch extends CustomerWithdraw
{
    public $datetime_start;
    public $datetime_end;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'payment_history_id', 'created_by', 'updated_by'], 'integer'],
            [['amount'], 'number'],
            [['remarks', 'status', 'created_at', 'updated_at', 'outletId'], 'safe'],

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

        //if(!Yii::$app->asm->can('index-full')){
            $query->andFilterWhere([
                'customer_withdraw.created_by' => Yii::$app->user->id,
            ]);
        //}

        $query->andFilterWhere(['>=', 'created_at', $this->datetime_start])
            ->andFilterWhere(['<', 'created_at', $this->datetime_end]);

        // grid filtering conditions
        $query->andFilterWhere([
            'customer_withdraw.id' => $this->id,
            'customer_withdraw.payment_history_id' => $this->payment_history_id,
            'customer_withdraw.amount' => $this->amount,
            'customer_withdraw.created_by' => $this->created_by,
        ]);

        $query->andFilterWhere(['like', 'customer_withdraw.remarks', $this->remarks])
            ->andFilterWhere(['like', 'customer_withdraw.outletId', $this->outletId])
            ->andFilterWhere(['like', 'customer_withdraw.status', $this->status]);

        $query->orderBy('customer_withdraw.id DESC');

        return $dataProvider;
    }
}
