<?php

namespace app\models;

use app\components\DateTimeUtility;
use app\components\Utility;
use kartik\daterange\DateRangeBehavior;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\WarehousePayment;

/**
 * WarehousePaymentSearch represents the model behind the search form about `app\models\WarehousePayment`.
 */
class WarehousePaymentSearch extends WarehousePayment
{
    public $datetime_start;
    public $datetime_end;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'warehouse_id', 'month', 'year', 'user_id', 'outletId'], 'integer'],
            [['payment_amount'], 'number'],
            [['created_at', 'updated_at', 'created_to'], 'safe'],

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
        $query = WarehousePayment::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $permissions = Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->getId());
        if (!isset($permissions['ALL_USER_DATA'])) {
            $query->andFilterWhere([
                'user_id' => Yii::$app->user->id,
            ]);
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'warehouse_id' => $this->warehouse_id,
            'outletId' => $this->outletId,
            'payment_amount' => $this->payment_amount,
            'month' => $this->month,
            'year' => $this->year,
            'user_id' => $this->user_id,
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

        $query->with('warehouse','user','paymentType');
        $query->orderBy('id DESC');

        return $dataProvider;
    }
}
