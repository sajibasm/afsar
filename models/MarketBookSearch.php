<?php

namespace app\models;

use app\components\DateTimeUtility;
use app\components\Utility;
use kartik\daterange\DateRangeBehavior;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\MarketBook;

/**
 * MarketBookSearch represents the model behind the search form about `app\models\MarketBook`.
 */
class MarketBookSearch extends MarketBook
{
    public $created_to;
    public $datetime_start;
    public $datetime_end;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['market_sales_id', 'client_id', 'item_id', 'brand_id', 'size_id', 'quantity'], 'integer'],
            [['unit', 'created_at', 'updated_at', 'status'], 'safe'],
            [['cost_amount', 'sales_amount', 'total_amount'], 'number'],

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
        $query = MarketBook::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        //Utility::debug($this->client_id);

        //if(!Yii::$app->asm->can('index-full')){
            $query->andFilterWhere([
                'user_id' => Yii::$app->user->id,
            ]);
        //}

        $query->andFilterWhere([
            'client_id' => $this->client_id,
            'item_id' => $this->item_id,
            'brand_id' => $this->brand_id,
            'size_id' => $this->size_id,
            //'sales_id' => 0,
        ]);


        $query->andFilterWhere(['>=', 'created_at', $this->datetime_start])
            ->andFilterWhere(['<', 'created_at', $this->datetime_end]);

        $query->orderBy([
            'status'=>SORT_DESC,
            'created_at'=>SORT_DESC,
        ]);

//        $query->orderBy([
//            'market_sales_id'=>SORT_DESC,
//        ]);


        return $dataProvider;
    }

    public function marketBookCreate()
    {
        $query = MarketBook::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->andFilterWhere([
            'client_id' => $this->client_id,
        ]);

        $query->orderBy([
            'status'=>SORT_DESC,
            'created_at'=>SORT_DESC,
        ]);

        return $dataProvider;
    }

}
