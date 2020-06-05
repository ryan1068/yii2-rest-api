<?php
namespace api\components;

use yii\data\DataProviderInterface;

/**
 * 序列号返回数据
 * Class Serializer
 * @package api\components
 */
class Serializer extends \yii\rest\Serializer
{
    /**
     * @param mixed $data
     * @return array|mixed
     */
    public function serialize($data)
    {
        if ($data instanceof DataProviderInterface) {
            return $this->serializeList($data);
        }
        if (isset($data['list']) && $data['list'] instanceof DataProviderInterface) {
            $listData = $this->serializeList($data['list']);
            unset($data['list']);
            $data = array_merge($data, $listData);
        }
        return parent::serialize($data);
    }

    /**
     * @param DataProviderInterface $data
     * @return array
     */
    protected function serializeList($data)
    {
        $listData['list'] = parent::serialize($data);
        if ($data->getPagination()) {
            $listData['page'] = [
                'totalCount' => $data->getPagination()->totalCount,
                'totalPage' => $data->getPagination()->getPageCount(),
                'currentPage' => $data->getPagination()->getPage() + 1,
                'pageSize' => $data->getPagination()->getPageSize(),
            ];
        }
        return $listData;
    }
}