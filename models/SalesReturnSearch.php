<?php

namespace app\models;

use kartik\daterange\DateRangeBehavior;
use Yii;
use app\components\DateTimeUtility;
use app\components\OutletUtility;
use yii\base\Model;
use yii\data\ActiveDataProvider;


/**
 * SalesReturnSearch represents the model behind the search form about `app\models\SalesReturn`.
 */
class SalesReturnSearch extends SalesReturn
{
    public $datetime_start;
    public $datetime_end;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sales_return_id', 'user_id', 'sales_id', 'client_id'], 'integer'],
            [['memo_id', 'client_name', 'client_mobile', 'remarks', 'created_at', 'updated_at', 'created_to', 'outletId'], 'safe'],
            [['refund_amount', 'cut_off_amount', 'total_amount'], 'number'],

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
    public function search($params, $isToday = false)
    {
        $query = SalesReturn::find();
        $query->where(['outletId' => array_keys(OutletUtility::getUserOutlet())]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

//        if(!Yii::$app->asm->can('index-full')){
            $query->andFilterWhere([
                'user_id' => Yii::$app->user->id,
            ]);
        //}

        $query->andFilterWhere([
            'sales_return_id' => $this->sales_return_id,
            'user_id' => $this->user_id,
            'sales_id' => $this->sales_id,
            'client_id' => $this->client_id,
            'refund_amount' => $this->refund_amount,
            'cut_off_amount' => $this->cut_off_amount,
            'total_amount' => $this->total_amount,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'memo_id', $this->memo_id])
            ->andFilterWhere(['like', 'client_name', $this->client_name])
            ->andFilterWhere(['like', 'client_mobile', $this->client_mobile])
            ->andFilterWhere(['like', 'remarks', $this->remarks])
            ->andFilterWhere(['like', 'outletId', $this->outletId]);

        if($isToday){
            $query->andFilterWhere([
                'BETWEEN',
                'created_at',
                DateTimeUtility::getTodayStartTime(),
                DateTimeUtility::getTodayEndTime()
            ]);
        }else{
            $query->andFilterWhere(['>=', 'created_at', $this->datetime_start])
                ->andFilterWhere(['<', 'created_at', $this->datetime_end]);
        }

        $query->orderBy('sales_return_id DESC');


        return $dataProvider;
    }
}
