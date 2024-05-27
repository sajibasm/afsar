<?php

namespace app\models;

use app\components\DateTimeUtility;
use app\components\OutletUtility;
use kartik\daterange\DateRangeBehavior;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CashHandReceived;

/**
 * CashHandReceivedSearch represents the model behind the search form about `app\models\CashHandReceived`.
 */
class CashHandReceivedSearch extends CashHandReceived
{
    public $datetime_start;
    public $datetime_end;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'outletId'], 'integer'],
            [['received_amount'], 'number'],
            [['remarks', 'created_at', 'updated_at', 'created_to', 'status'], 'safe'],
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
        $query = CashHandReceived::find();
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
                'user_id' => Yii::$app->user->id,
            ]);
        //}

        // grid filtering conditions
        $query->andFilterWhere([
            'received_amount' => $this->received_amount,
            'outletId' => $this->outletId,
            'statusd' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'remarks', $this->remarks]);


        if($isToday){
            $query->andFilterWhere(['>=', 'created_at', DateTimeUtility::getTodayStartTime()]);
            $query->andFilterWhere(['<=', 'created_at', DateTimeUtility::getTodayEndTime()]);
        }else{
            $query->andFilterWhere(['>=', 'created_at', $this->datetime_start])
                ->andFilterWhere(['<', 'created_at', $this->datetime_end]);
        }

        $query->orderBy('id DESC');

        return $dataProvider;
    }
}
