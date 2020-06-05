<?php

namespace api\searches;

use api\models\Org;
use api\resources\Area;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Class AreaSearch
 * @package api\searches
 */
class AreaSearch extends Model
{
    public $orgId;
    public $name;
    public $areaId;
    public $pagination;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['name'], 'string'],
            [['orgId', 'areaId'], 'integer'],
            [['pagination'], 'safe'],
        ];
    }

    /**
     * @return ActiveDataProvider
     */
    public function search()
    {
        $query = Area::find()->active();

        if ($this->orgId) {
            $query->joinWith('orgs')
                ->andWhere([Org::withDatabaseName('id') => $this->orgId])
                ->andWhere([Org::withDatabaseName('is_del') => Org::IS_DEL_NO]);
        }

        $query->andFilterWhere(['LIKE', 'short_name', $this->name]);
        $query->andFilterWhere(['id' => $this->areaId]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query->defaultOrderBy(),
        ]);

        if (isset($this->pagination) && empty($this->pagination)) {
            $dataProvider->pagination = false;
        }

        if (!$this->validate()) {
            $query->where('0=1');
        }

        return $dataProvider;
    }
}