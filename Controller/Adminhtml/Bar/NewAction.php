<?php

declare(strict_types=1);

namespace Panth\NotificationBar\Controller\Adminhtml\Bar;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;

class NewAction extends Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Panth_NotificationBar::manage';

    /**
     * Forward to edit action
     *
     * @return \Magento\Framework\Controller\Result\Forward
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
        $resultForward = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_FORWARD);

        return $resultForward->forward('edit');
    }
}
