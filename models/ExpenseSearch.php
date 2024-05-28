<?php

namespace app\models;

use app\components\SystemSettings;
use app\components\DateTimeUtility;
use app\components\OutletUtility;
use app\components\Utility;
use kartik\daterange\DateRangeBehavior;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Expense;

/**
 * ExpenseSearch represents the model behind the search form about `app\models\Expense`.
 */
class ExpenseSearch extends Expense
{
    public $datetime_start;
    public $datetime_end;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['expense_id', 'expense_type_id', 'user_id', 'outletId'], 'integer'],
            [['expense_amount'], 'number'],
            [['expense_remarks', 'created_at', 'updated_at', 'type', 'created_to'], 'safe'],

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

        $permissions = Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->getId());
        if (!isset($permissions['ALL_USER_DATA'])) {
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
            $query->andFilterWhere(['>=', 'created_at', $this->datetime_start])
                ->andFilterWhere(['<', 'created_at', $this->datetime_end]);
        }

        $query->with('user','expenseType', 'paymentType');
        $query->andFilterWhere(['like', 'expense_remarks', $this->expense_remarks]);
        //$query->orderBy('expense_id DESC');

        //Utility::debug($query->createCommand()->getRawSql());

        return $dataProvider;
    }
}
