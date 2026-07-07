<?php
declare(strict_types=1);

namespace Panth\NotificationBar\Model\Bar;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Panth\NotificationBar\Model\ResourceModel\Bar\CollectionFactory;

class DataProvider extends AbstractDataProvider
{
    private DataPersistorInterface $dataPersistor;

    private ?array $loadedData = null;

    private const MULTI_VALUE_FIELDS = [
        'store_ids',
        'customer_groups',
        'target_page_types',
        'target_countries',
    ];

    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData(): array
    {
        if ($this->loadedData !== null) {
            return $this->loadedData;
        }

        $this->loadedData = [];
        $items = $this->collection->getItems();

        foreach ($items as $bar) {
            $barData = $bar->getData();
            $barData = $this->convertMultiValueFields($barData);
            $this->loadedData[$bar->getId()] = $barData;
        }

        $persistedData = $this->dataPersistor->get('panth_notification_bar');
        if (!empty($persistedData)) {
            $bar = $this->collection->getNewEmptyItem();
            $bar->setData($persistedData);
            $barData = $bar->getData();
            $barData = $this->convertMultiValueFields($barData);
            $this->loadedData[$bar->getId()] = $barData;
            $this->dataPersistor->clear('panth_notification_bar');
        }

        return $this->loadedData;
    }

    private function convertMultiValueFields(array $data): array
    {
        foreach (self::MULTI_VALUE_FIELDS as $field) {
            if (isset($data[$field]) && is_string($data[$field]) && $data[$field] !== '') {
                $data[$field] = explode(',', $data[$field]);
            }
        }

        return $data;
    }
}
