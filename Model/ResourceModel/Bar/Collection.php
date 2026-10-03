<?php
declare(strict_types=1);

namespace Panth\NotificationBar\Model\ResourceModel\Bar;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Panth\NotificationBar\Model\Bar as BarModel;
use Panth\NotificationBar\Model\ResourceModel\Bar as BarResource;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'bar_id';

    protected function _construct(): void
    {
        $this->_init(BarModel::class, BarResource::class);
    }

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

    public function addPositionFilter(string $position): self
    {
        $this->addFieldToFilter('position', $position);

        return $this;
    }
}
