<?php

namespace app\models;

use app\components\SystemSettings;
use app\components\DateTimeUtility;
use app\components\OutletUtility;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ClientPaymentHistory;

/**
 * ClientPaymentHistorySearch represents the model behind the search form about `app\models\ClientPaymentHistory`.
 */
class ClientPaymentHistorySearch extends ClientPaymentHistory
{
    public $received_to;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_payment_history_id', 'client_id', 'user_id', 'payment_type_id'], 'integer'],
            [['received_amount'], 'number'],
            [['received_to'], 'string'],
            [['remarks', 'received_at', 'updated_at', 'received_type', 'received_to'], 'safe'],
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
        $query = ClientPaymentHistory::find();
        $query->where(['outletId' => array_keys(OutletUtility::getUserOutlet())]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => SystemSettings::getPerPageRecords(),
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if(!empty($this->received_at)){
            $query->andFilterWhere([
                'BETWEEN',
                'received_at',
                DateTimeUtility::getStartTime(false, DateTimeUtility::getDate($this->received_at)),
                DateTimeUtility::getEndTime(false, DateTimeUtility::getDate($this->received_to))
            ]);
        }

        if(!Yii::$app->asm->can('index-full')){
            $query->andFilterWhere([
                'user_id' => Yii::$app->user->id,
            ]);
        }

        $query->andFilterWhere([
            'client_payment_history_id' => $this->client_payment_history_id,
            'client_id' => $this->client_id,
            'user_id' => $this->user_id,
            'received_amount' => $this->received_amount,
            'received_type' => $this->received_type,
            'payment_type_id' => $this->payment_type_id,
            'outletId' => $this->outletId,
        ]);


        $query->andFilterWhere(['like', 'remarks', $this->remarks]);
        $query->orderBy('client_payment_history_id DESC');

        return $dataProvider;
    }
}
