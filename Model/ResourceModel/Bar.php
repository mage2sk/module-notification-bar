<?php

declare(strict_types=1);

namespace Panth\NotificationBar\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Bar extends AbstractDb
{
    /**
     * @var string
     */
    const TABLE_NAME = 'panth_notification_bar';

    /**
     * @inheritdoc
     */
    protected function _construct(): void
    {
        $this->_init(self::TABLE_NAME, 'bar_id');
    }
}
