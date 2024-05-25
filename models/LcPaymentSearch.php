<?php

namespace app\models;

use app\components\SystemSettings;
use app\components\DateTimeUtility;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\LcPayment;

/**
 * LcPaymentSearch represents the model behind the search form about `app\models\LcPayment`.
 */
class LcPaymentSearch extends LcPayment
{
    public $lc;
    public $lcPaymentType;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lc_payment_id', 'lc_id', 'lc_payment_type', 'user_id'], 'integer'],
            [['amount'], 'number'],
            [['remarks', 'created_at', 'updated_at', 'lc_payment_type', 'lc', 'lcPaymentType', 'created_to'], 'safe'],
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
    public function search($params, $isToday)
    {
        $query = LcPayment::find();

        $query->joinWith(['lc', 'lcPaymentType']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => SystemSettings::getPerPageRecords(),
            ],
            'sort'=> ['defaultOrder' => ['lc_payment_id'=>SORT_DESC]]
        ]);

        $dataProvider->sort->attributes['lc'] = [
            'asc' => ['lc.lc_name' => SORT_ASC],
            'desc' => ['lc.lc_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['lcPaymentType'] = [
            'asc' => ['lc_payment_type.lc_payment_type_name' => SORT_ASC],
            'desc' => ['lc_payment_type.lc_payment_type_name' => SORT_DESC],
        ];


        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if(!Yii::$app->asm->can('index-full')){
            $query->andFilterWhere([
                'user_id' => Yii::$app->user->id,
            ]);
        }

        $query->andFilterWhere([
            'lc_payment_id' => $this->lc_payment_id,
            //'lc_payment_type' => $this->lc_payment_type,
            'user_id' => $this->user_id,
            'amount' => $this->amount,
        ]);

        $query->andFilterWhere(['like', 'remarks', $this->remarks])
            ->andFilterWhere(['like', 'lc.lc_name', $this->lc])
            ->andFilterWhere(['like', 'lc_payment_type.lc_payment_type_name', $this->lcPaymentType]);


        if($isToday){
            $query->andFilterWhere([
                'BETWEEN',
                'lc_payment.created_at',
                DateTimeUtility::getTodayStartTime(),
                DateTimeUtility::getTodayEndTime()
            ]);
        }else{
            if(!empty($this->created_at)){
                $query->andFilterWhere([
                    'BETWEEN',
                    'lc_payment.created_at',
                    DateTimeUtility::getStartTime(false, DateTimeUtility::getDate($this->created_at)),
                    DateTimeUtility::getEndTime(false, DateTimeUtility::getDate($this->created_to))
                ]);
            }
        }

        return $dataProvider;
    }
}
