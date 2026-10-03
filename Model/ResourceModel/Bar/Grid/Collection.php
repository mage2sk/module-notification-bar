<?php
declare(strict_types=1);

namespace Panth\NotificationBar\Model\ResourceModel\Bar\Grid;

use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Psr\Log\LoggerInterface;

class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult implements SearchResultInterface
{
    protected $_idFieldName = 'bar_id';

    protected $aggregations;

    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        string $mainTable = 'panth_notification_bar',
        string $resourceModel = \Panth\NotificationBar\Model\ResourceModel\Bar::class,
        ?AdapterInterface $connection = null,
        ?AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $mainTable,
            $resourceModel,
            $connection,
            $resource
        );
    }

    protected function _initSelect(): self
    {
        parent::_initSelect();
        $this->addFilterToMap('bar_id', 'main_table.bar_id');
        return $this;
    }

    protected function _afterLoad(): self
    {
        parent::_afterLoad();
        foreach ($this->_items as $item) {
            $item->setId($item->getData('bar_id'));
        }
        return $this;
    }

    public function getAggregations(): AggregationInterface
    {
        return $this->aggregations;
    }

    public function setAggregations($aggregations): self
    {
        $this->aggregations = $aggregations;
        return $this;
    }

    public function getSearchCriteria(): ?SearchCriteriaInterface
    {
        return null;
    }

    public function setSearchCriteria(?SearchCriteriaInterface $searchCriteria = null): self
    {
        return $this;
    }

    public function getTotalCount(): int
    {
        return $this->getSize();
    }

    public function setTotalCount($totalCount): self
    {
        return $this;
    }

    public function setItems(?array $items = null): self
    {
        return $this;
    }
}
