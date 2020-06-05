<?php
/**
 * User: Ryan
 * Date: 2020/4/13
 * Time: 17:35
 */

namespace api\resources;


/**
 * Class YecaiAdminUser
 * @package api\resources
 */
class YecaiAdminUser extends \api\models\YecaiAdminUser
{
    /**
     * @return array
     */
    public function fields()
    {
        return [
            'id',
            'account',
            'nickname',
            'tel',
            'status',
            'createdDate' => function () {
                return date('Y-m-d H:i', $this->created_at);
            },
        ];
    }
}