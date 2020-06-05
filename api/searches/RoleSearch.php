<?php

namespace api\searches;

use api\resources\Role;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Class RoleSearch
 * @package api\searches
 */
class RoleSearch extends Model
{
    public $name;
    public $type;
    public $pagination;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['name'], 'string'],
            [['type'], 'integer'],
            [['pagination'], 'safe'],
        ];
    }

    /**
     * @return ActiveDataProvider
     */
    public function search()
    {
        $query = Role::find()->active();

        $query->andWhere(['<>', 'role_key', Role::ROLE_ADMINISTRATOR]);
        $query->andFilterWhere(['LIKE', 'name', $this->name]);
        $query->andFilterWhere(['type' => $this->type]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query->defaultOrderBy()
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
