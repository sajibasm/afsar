<?php

namespace app\models;

use app\components\SystemSettings;
use app\components\DateTimeUtility;
use app\components\OutletUtility;
use app\components\Utility;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\DepositBook;

/**
 * DepositBookSearch represents the model behind the search form about `app\models\DepositBook`.
 */
class DepositBookSearch extends DepositBook
{
    public $created_to;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'bank_id', 'branch_id', 'amountFrom', 'amountTo'], 'integer'],
            [['deposit_in', 'deposit_out'], 'number'],
            [['created_to'], 'string'],
            [['reference_id', 'source', 'remarks', 'created_at', 'updated_at', 'created_to', 'typeFilter', 'outletId'], 'safe'],
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
        $query = DepositBook::find();
        $query->where(['outletId' => array_keys(OutletUtility::getUserOutlet())]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => SystemSettings::getPerPageRecords(),
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if(!empty($this->typeFilter)){

            if($this->typeFilter==DepositBook::TYPE_FILTER_INFLOW){
                $query->andFilterWhere(['deposit_out'=>0]);
                if(!empty($this->amountFrom)){
                    $query->andFilterWhere(['>=', 'deposit_in', $this->amountFrom]);
                }
                if(!empty($this->amountTo)){
                    $query->andFilterWhere(['<=', 'deposit_in', $this->amountTo]);
                }
            }elseif ($this->typeFilter==DepositBook::TYPE_FILTER_OUTFLOW){
                $query->andFilterWhere(['deposit_in'=>0]);
                if(!empty($this->amountFrom)){
                    $query->andFilterWhere(['>=', 'deposit_out', $this->amountFrom]);
                }
                if(!empty($this->amountTo)){
                    $query->andFilterWhere(['<=', 'deposit_out', $this->amountTo]);
                }
            }
        }

        if($isToday){
            $query->andFilterWhere(['>=', 'created_at', DateTimeUtility::getTodayStartTime()]);
            $query->andFilterWhere(['<=', 'created_at', DateTimeUtility::getTodayEndTime()]);
        }else {
            if (!empty($this->created_at)) {
                $query->andFilterWhere([
                    'BETWEEN',
                    'created_at',
                    DateTimeUtility::getStartTime(false, DateTimeUtility::getDate($this->created_at)),
                    DateTimeUtility::getEndTime(false, DateTimeUtility::getDate($this->created_to))
                ]);
            }
        }

        if(!Yii::$app->asm->can('index-full')){
            $query->andFilterWhere([
                'user_id' => Yii::$app->user->id,
            ]);
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'bank_id' => $this->bank_id,
            'branch_id' => $this->branch_id,
        ]);

        $query->andFilterWhere(['like', 'reference_id', $this->reference_id])
            ->andFilterWhere(['like', 'source', $this->source])
            ->andFilterWhere(['like', 'remarks', $this->remarks])
            ->andFilterWhere(['like', 'outletId', $this->outletId]);

        $query->with('clientPaymentHistory', 'bank', 'branch', 'paymentType');

        return $dataProvider;

    }
}
