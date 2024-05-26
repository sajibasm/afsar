<?php

namespace app\models;

use app\components\DateTimeUtility;
use app\components\Utility;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\WarehousePayment;

/**
 * WarehousePaymentSearch represents the model behind the search form about `app\models\WarehousePayment`.
 */
class WarehousePaymentSearch extends WarehousePayment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'warehouse_id', 'month', 'year', 'user_id', 'outletId'], 'integer'],
            [['payment_amount'], 'number'],
            [['created_at', 'updated_at', 'created_to'], 'safe'],
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
            if(!empty($this->created_at)){
                $query->andFilterWhere([
                    'BETWEEN',
                    'created_at',
                    DateTimeUtility::getStartTime(false, DateTimeUtility::getDate($this->created_at)),
                    DateTimeUtility::getEndTime(false, DateTimeUtility::getDate($this->created_to))
                ]);
            }
        }

        $query->with('warehouse','user','paymentType');
        $query->orderBy('id DESC');

        return $dataProvider;
    }
}
