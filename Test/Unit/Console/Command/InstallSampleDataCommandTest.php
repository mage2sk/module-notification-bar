<?php
declare(strict_types=1);

namespace Panth\NotificationBar\Test\Unit\Console\Command;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Panth\NotificationBar\Console\Command\InstallSampleDataCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class InstallSampleDataCommandTest extends TestCase
{
    /**
     * @var array
     */
    private array $inserted = [];

    private function command(bool $tableExists, ?string $failName = null): InstallSampleDataCommand
    {
        $this->inserted = [];
        $connection = $this->createStub(AdapterInterface::class);
        $connection->method('isTableExists')->willReturn($tableExists);
        $connection->method('insert')->willReturnCallback(function ($table, array $row) use ($failName) {
            if ($row['name'] === $failName) {
                throw new \RuntimeException('duplicate');
            }
            $this->inserted[] = [$table, $row];
            return 1;
        });
        $resource = $this->createStub(ResourceConnection::class);
        $resource->method('getConnection')->willReturn($connection);
        $resource->method('getTableName')->willReturnCallback(static fn($name) => 'pfx_' . $name);

        return new InstallSampleDataCommand($resource);
    }

    public function testCommandName(): void
    {
        $this->assertSame('panth:notificationbar:install-sample-data', $this->command(true)->getName());
    }

    public function testFailsWhenTableIsMissing(): void
    {
        $tester = new CommandTester($this->command(false));

        $this->assertSame(Command::FAILURE, $tester->execute([]));
        $this->assertStringContainsString('does not exist. Run setup:upgrade first.', $tester->getDisplay());
        $this->assertSame([], $this->inserted);
    }

    public function testInsertsFiveSampleBarsIntoPrefixedTable(): void
    {
        $tester = new CommandTester($this->command(true));

        $this->assertSame(Command::SUCCESS, $tester->execute([]));
        $this->assertCount(5, $this->inserted);
        $this->assertSame(['pfx_panth_notification_bar'], array_values(array_unique(array_column($this->inserted, 0))));
        $this->assertStringContainsString('Done! 5 sample notification bar(s) created.', $tester->getDisplay());

        $positions = ['top_fixed', 'top_static', 'bottom_fixed', 'bottom_floating'];
        foreach (array_column($this->inserted, 1) as $row) {
            $this->assertContains($row['position'], $positions);
            $this->assertSame('0', $row['store_ids']);
            $this->assertSame('all', $row['page_targeting']);
        }
    }

    public function testCountdownSampleEndsInTheFuture(): void
    {
        (new CommandTester($this->command(true)))->execute([]);

        $countdown = array_values(array_filter(
            array_column($this->inserted, 1),
            static fn($row) => !empty($row['countdown_enabled'])
        ));
        $this->assertCount(1, $countdown);
        $this->assertStringContainsString('{countdown}', $countdown[0]['content']);
        $this->assertGreaterThan(time(), strtotime($countdown[0]['countdown_end_date']));
    }

    public function testFailedInsertIsReportedAndOthersContinue(): void
    {
        $tester = new CommandTester($this->command(true, 'Cookie Consent'));

        $this->assertSame(Command::SUCCESS, $tester->execute([]));
        $this->assertCount(4, $this->inserted);
        $display = $tester->getDisplay();
        $this->assertStringContainsString('Failed to create "Cookie Consent": duplicate', $display);
        $this->assertStringContainsString('Done! 4 sample notification bar(s) created.', $display);
    }
}
