<?php

namespace app\models;

use app\components\SystemSettings;
use app\components\DateTimeUtility;
use app\components\OutletUtility;
use app\components\Utility;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Expense;

/**
 * ExpenseSearch represents the model behind the search form about `app\models\Expense`.
 */
class ExpenseSearch extends Expense
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['expense_id', 'expense_type_id', 'user_id', 'outletId'], 'integer'],
            [['expense_amount'], 'number'],
            [['expense_remarks', 'created_at', 'updated_at', 'type', 'created_to'], 'safe'],
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
        $query = Expense::find();
        $query->where(['outletId' => array_keys(OutletUtility::getUserOutlet())]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => SystemSettings::getPerPageRecords(),
            ],
            'sort' => [
                'defaultOrder' => [
                    //'id' => SORT_DESC,
                    'created_at' => SORT_DESC,
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if(!Yii::$app->asm->can('index-full')){
            $query->andFilterWhere([
                'user_id' => Yii::$app->user->id,
            ]);
        }

        $query->andFilterWhere([
            'expense_id' => $this->expense_id,
            'expense_type_id' => $this->expense_type_id,
            'outletId' => $this->outletId,
            'expense_amount' => $this->expense_amount,
            'type'=>$this->type,
            'source'=>self::SOURCE_INTERNAL
        ]);

        if($isToday){
            $query->andFilterWhere([
                'BETWEEN',
                'created_at',
                DateTimeUtility::getTodayStartTime(),
                DateTimeUtility::getTodayEndTime()
            ]);
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

        $query->with('user','expenseType', 'paymentType');
        $query->andFilterWhere(['like', 'expense_remarks', $this->expense_remarks]);
        //$query->orderBy('expense_id DESC');

        //Utility::debug($query->createCommand()->getRawSql());

        return $dataProvider;
    }
}
