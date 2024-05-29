<?php

namespace app\models;

use app\components\SystemSettings;
use app\components\DateTimeUtility;
use app\components\Utility;
use kartik\daterange\DateRangeBehavior;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CashBook;

/**
 * CashBookSearch represents the model behind the search form about `app\models\CashBook`.
 */
class CashBookSearch extends CashBook
{
    public $created_to;

    public $created_at;
    public $datetime_start;
    public $datetime_end;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'amountFrom', 'amountTo'], 'integer'],
            [['cash_in', 'cash_out'], 'number'],
            [['source', 'reference_id', 'remarks', 'created_at', 'updated_at', 'created_to', 'typeFilter', 'outletId'], 'safe'],

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
        $query = CashBook::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => SystemSettings::getPerPageRecords(),
            ],
            'sort' => [
                'defaultOrder' => [
                    //'id' => SORT_DESC,
                    'created_at' => SORT_ASC,
                ]
            ],
        ]);


        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'cash_book.id' => $this->id,
            'cash_book.source' => $this->source,
        ]);

        //if(!Yii::$app->asm->can('index-full')){
            $query->andFilterWhere([
                'cash_book.ref_user_id' => Yii::$app->user->id,
            ]);
        //}

        if(!empty($this->typeFilter)){

            if($this->typeFilter==CashBook::TYPE_FILTER_INFLOW){
                $query->andFilterWhere(['cash_book.cash_out'=>0]);
                if(!empty($this->amountFrom)){
                    $query->andFilterWhere(['>=', 'cash_book.cash_in', $this->amountFrom]);
                }

                if(!empty($this->amountTo)){
                    $query->andFilterWhere(['<=', 'cash_book.cash_in', $this->amountTo]);
                }
            }elseif ($this->typeFilter==CashBook::TYPE_FILTER_OUTFLOW){
                $query->andFilterWhere(['cash_in'=>0]);
                if(!empty($this->amountFrom)){
                    $query->andFilterWhere(['>=', 'cash_book.cash_out', $this->amountFrom]);
                }
                if(!empty($this->amountTo)){
                    $query->andFilterWhere(['<=', 'cash_book.cash_out', $this->amountTo]);
                }
            }
        }

        if($isToday){
            $query->andFilterWhere(['>=', 'cash_book.created_at', DateTimeUtility::getTodayStartTime()]);
            $query->andFilterWhere(['<=', 'cash_book.created_at', DateTimeUtility::getTodayEndTime()]);
        }else {
            $query->andFilterWhere(['>=', 'cash_book.created_at', $this->datetime_start])
                ->andFilterWhere(['<', 'cash_book.created_at', $this->datetime_end]);
        }

        $query->andFilterWhere(['like', 'remarks', $this->remarks])
            ->andFilterWhere(['like', 'outletId', $this->outletId]);
        $query->orderBy(['id'=>SORT_DESC]);
        return $dataProvider;
    }
}
