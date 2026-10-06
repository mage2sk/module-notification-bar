<?php
declare(strict_types=1);

namespace Panth\NotificationBar\Test\Unit\Controller\Adminhtml\Bar;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Model\Context as ModelContext;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Registry;
use Panth\NotificationBar\Model\Bar;
use Panth\NotificationBar\Model\BarFactory;
use PHPUnit\Framework\TestCase;

/**
 * Shared admin controller wiring that records redirects and flash messages.
 */
abstract class ControllerTestCase extends TestCase
{
    /**
     * @var array
     */
    protected array $redirect = [];

    /**
     * @var array
     */
    protected array $messages = [];

    protected function buildContext(array $params = [], $post = null): Context
    {
        $this->redirect = [];
        $this->messages = ['success' => [], 'error' => [], 'exception' => []];

        $request = $this->createStub(Http::class);
        $request->method('getParam')->willReturnCallback(
            static fn($key, $default = null) => $params[$key] ?? $default
        );
        $request->method('getPostValue')->willReturn($post);

        $redirect = $this->createStub(Redirect::class);
        $redirect->method('setPath')->willReturnCallback(
            function ($path, $args = []) use (&$redirect) {
                $this->redirect = ['path' => $path, 'params' => $args];
                return $redirect;
            }
        );
        $redirectFactory = $this->createStub(RedirectFactory::class);
        $redirectFactory->method('create')->willReturn($redirect);

        $messageManager = $this->createStub(ManagerInterface::class);
        $messageManager->method('addSuccessMessage')->willReturnCallback(
            function ($message) use (&$messageManager) {
                $this->messages['success'][] = (string)$message;
                return $messageManager;
            }
        );
        $messageManager->method('addErrorMessage')->willReturnCallback(
            function ($message) use (&$messageManager) {
                $this->messages['error'][] = (string)$message;
                return $messageManager;
            }
        );
        $messageManager->method('addExceptionMessage')->willReturnCallback(
            function ($exception, $message = null) use (&$messageManager) {
                $this->messages['exception'][] = (string)$message;
                return $messageManager;
            }
        );

        $context = $this->createStub(Context::class);
        $context->method('getRequest')->willReturn($request);
        $context->method('getResultRedirectFactory')->willReturn($redirectFactory);
        $context->method('getMessageManager')->willReturn($messageManager);

        return $context;
    }

    protected function bar(array $data = []): Bar
    {
        $resource = $this->createStub(AbstractDb::class);
        $resource->method('getIdFieldName')->willReturn('bar_id');

        $bar = new Bar($this->createStub(ModelContext::class), $this->createStub(Registry::class), $resource);
        $bar->setData($data);

        return $bar;
    }

    protected function barFactory(Bar ...$bars): BarFactory
    {
        $factory = $this->createStub(BarFactory::class);
        $factory->method('create')->willReturnOnConsecutiveCalls(...($bars ?: [$this->bar()]));

        return $factory;
    }
}
