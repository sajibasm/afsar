<?php

namespace app\models;

use app\components\OutletUtility;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ProductStatementOutlet;

/**
 * ProductStatementOutletSearch represents the model behind the search form about `app\models\ProductStatementOutlet`.
 */
class ProductStatementOutletSearch extends ProductStatementOutlet
{
    public $datetime_start;
    public $datetime_end;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_statement_outlet_id', 'reference_id'], 'integer'],
            [['quantity'], 'number'],
            [['type', 'remarks', 'created_at', 'updated_at', 'outlet_id', 'item_id', 'brand_id', 'size_id', 'user_id'], 'safe'],

            [['created_at', 'datetime_start', 'datetime_end'], 'safe'],
            [['created_at'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],

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
        $query = ProductStatementOutlet::find();
        $query->where(['outletId' => array_keys(OutletUtility::getUserOutlet())]);
        $query->joinWith(['outletDetail', 'itemDetail', 'sizeDetail']);

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
            'quantity' => $this->quantity,
        ]);

        // Direct filters
        if (!empty($this->created_at) && strpos($this->created_at, ' - ') !== false) {
            list($startDate, $endDate) = explode(' - ', $this->created_at);
            $startDate .= ' 00:00:00';
            $endDate .= ' 23:59:59';

            $query->andFilterWhere(['between', 'product_statement_outlet.createdAt', $startDate, $endDate]);
        } else {
            $query->andFilterWhere(['product_statement_outlet.created_at' => $this->created_at]);
        }


        $query->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'outlet.name', $this->outlet_id])
            ->andFilterWhere(['like', 'item.item_name', $this->item_id])
            ->andFilterWhere(['like', 'brand.brand_name', $this->brand_id])
            ->andFilterWhere(['like', 'size.size_name', $this->size_id])
            ->andFilterWhere(['like', 'remarks', $this->remarks]);

        $query->with(['outletDetail', 'itemDetail', 'brandDetail', 'sizeDetail']);

        return $dataProvider;
    }
}
