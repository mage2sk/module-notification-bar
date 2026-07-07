<?php
declare(strict_types=1);

namespace Panth\NotificationBar\Block\Adminhtml\Bar\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\UrlInterface;

class GenericButton
{
    private UrlInterface $urlBuilder;

    private Context $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
        $this->urlBuilder = $context->getUrlBuilder();
    }

    public function getBarId(): ?int
    {
        $barId = $this->context->getRequest()->getParam('bar_id');
        return $barId ? (int)$barId : null;
    }

    public function getUrl(string $route = '', array $params = []): string
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
}
