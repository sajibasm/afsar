<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Size;

/**
 * SizeSearch represents the model behind the search form about `app\models\Size`.
 */
class SizeSearch extends Size
{
    public $brand;
    public $item;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['size_id', 'brand_id', 'item_id'], 'integer'],
            [['size_name', 'size_status'], 'safe'],
            [['item', 'brand'], 'safe'],
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
        $query = Size::find();
        //$query->joinWith(['item', 'brand']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            //'sort'=> ['defaultOrder' => ['item'=>SORT_ASC]]
        ]);

//        $dataProvider->sort->attributes['item'] = [
//            // The tables are the ones our relation are configured to
//            // in my case they are prefixed with "tbl_"
//            'asc' => ['item.item_name' => SORT_ASC],
//            'desc' => ['item.item_name' => SORT_DESC],
//        ];

//        $dataProvider->sort->attributes['brand'] = [
//            // The tables are the ones our relation are configured to
//            // in my case they are prefixed with "tbl_"
//            'asc' => ['brand.brand_name' => SORT_ASC],
//            'desc' => ['brand.brand_name' => SORT_DESC],
//        ];


        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'size_status' => $this->size_status
        ]);


        $query->andFilterWhere(['like', 'size_name', $this->size_name]);
        //->andFilterWhere(['like', 'item.item_name', $this->item])
        //->andFilterWhere(['like', 'brand.brand_name', $this->brand]);

        return $dataProvider;
    }
}
