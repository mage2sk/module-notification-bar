<?php

declare(strict_types=1);

namespace Panth\NotificationBar\Model\ResourceModel\Bar;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Panth\NotificationBar\Model\Bar as BarModel;
use Panth\NotificationBar\Model\ResourceModel\Bar as BarResource;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'bar_id';

    /**
     * @inheritdoc
     */
    protected function _construct(): void
    {
        $this->_init(BarModel::class, BarResource::class);
    }

    /**
     * Add active filter with date range validation
     *
     * @return $this
     */
    public function addActiveFilter(): self
    {
        $now = (new \DateTime())->format('Y-m-d H:i:s');

        $this->addFieldToFilter('is_active', 1);
        $this->addFieldToFilter(
            'date_from',
            [
                ['null' => true],
                ['lteq' => $now]
            ]
        );
        $this->addFieldToFilter(
            'date_to',
            [
                ['null' => true],
                ['gteq' => $now]
            ]
        );

        return $this;
    }

    /**
     * Add store filter
     *
     * @param int $storeId
     * @return $this
     */
    public function addStoreFilter(int $storeId): self
    {
        $this->addFieldToFilter(
            'store_ids',
            [
                ['finset' => '0'],
                ['finset' => (string)$storeId]
            ]
        );

        return $this;
    }

    /**
     * Add position filter
     *
     * @param string $position
     * @return $this
     */
    public function addPositionFilter(string $position): self
    {
        $this->addFieldToFilter('position', $position);

        return $this;
    }
}
