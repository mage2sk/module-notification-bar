<?php
/**
 * Copyright © Panth Infotech. All rights reserved.
 */
declare(strict_types=1);

namespace Panth\NotificationBar\Console\Command;

use Magento\Framework\App\ResourceConnection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallSampleDataCommand extends Command
{
    public function __construct(
        private readonly ResourceConnection $resourceConnection
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('panth:notificationbar:install-sample-data');
        $this->setDescription('Install sample notification bars for Panth NotificationBar module');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('panth_notification_bar');

        if (!$connection->isTableExists($tableName)) {
            $output->writeln('<error>Table "panth_notification_bar" does not exist. Run setup:upgrade first.</error>');
            return Command::FAILURE;
        }

        $countdownEnd = (new \DateTime('+3 days'))->format('Y-m-d H:i:s');

        $samples = [
            [
                'name' => 'Welcome Promo Banner',
                'is_active' => 1,
                'bar_type' => 'promo',
                'position' => 'top_fixed',
                'sort_order' => 10,
                'content' => '🎉 Welcome! Use code <strong>WELCOME10</strong> for 10% off your first order!',
                'background_type' => 'color',
                'background_color' => '#0D9488',
                'text_color' => '#FFFFFF',
                'font_size' => 14,
                'bar_height' => 0,
                'bar_padding' => '10px 20px',
                'icon' => 'promo',
                'cta_enabled' => 1,
                'cta_text' => 'Shop Now',
                'cta_url' => '/sale',
                'cta_open_new_tab' => 0,
                'cta_bg_color' => '#FFFFFF',
                'cta_text_color' => '#0D9488',
                'countdown_enabled' => 0,
                'is_dismissible' => 1,
                'cookie_duration' => 3,
                'animation' => 'slide_down',
                'auto_close_seconds' => 0,
                'store_ids' => '0',
                'customer_groups' => '',
                'page_targeting' => 'all',
                'show_on_mobile' => 1,
                'show_on_desktop' => 1,
            ],
            [
                'name' => 'Free Shipping Notice',
                'is_active' => 1,
                'bar_type' => 'info',
                'position' => 'top_static',
                'sort_order' => 20,
                'content' => '🚚 Free shipping on orders over $50 — no code needed!',
                'background_type' => 'color',
                'background_color' => '#1E40AF',
                'text_color' => '#FFFFFF',
                'font_size' => 14,
                'bar_height' => 0,
                'bar_padding' => '10px 20px',
                'icon' => 'truck',
                'cta_enabled' => 0,
                'countdown_enabled' => 0,
                'is_dismissible' => 0,
                'cookie_duration' => 0,
                'animation' => 'fade_in',
                'auto_close_seconds' => 0,
                'store_ids' => '0',
                'customer_groups' => '',
                'page_targeting' => 'all',
                'show_on_mobile' => 1,
                'show_on_desktop' => 1,
            ],
            [
                'name' => 'Flash Sale Countdown',
                'is_active' => 1,
                'bar_type' => 'urgent',
                'position' => 'top_fixed',
                'sort_order' => 5,
                'content' => '⚡ Flash Sale! {countdown} — Up to 50% off everything!',
                'background_type' => 'color',
                'background_color' => '#DC2626',
                'text_color' => '#FFFFFF',
                'font_size' => 14,
                'bar_height' => 0,
                'bar_padding' => '12px 20px',
                'icon' => 'percent',
                'cta_enabled' => 0,
                'countdown_enabled' => 1,
                'countdown_end_date' => $countdownEnd,
                'countdown_label' => 'Ends in:',
                'countdown_expired_text' => 'Sale has ended!',
                'is_dismissible' => 1,
                'cookie_duration' => 0,
                'animation' => 'slide_down',
                'auto_close_seconds' => 0,
                'store_ids' => '0',
                'customer_groups' => '',
                'page_targeting' => 'all',
                'show_on_mobile' => 1,
                'show_on_desktop' => 1,
            ],
            [
                'name' => 'Cookie Consent',
                'is_active' => 1,
                'bar_type' => 'info',
                'position' => 'bottom_fixed',
                'sort_order' => 100,
                'content' => '🍪 We use cookies to improve your experience. By continuing to browse, you agree to our cookie policy.',
                'background_type' => 'color',
                'background_color' => '#374151',
                'text_color' => '#FFFFFF',
                'font_size' => 13,
                'bar_height' => 0,
                'bar_padding' => '12px 20px',
                'icon' => 'info',
                'cta_enabled' => 1,
                'cta_text' => 'Accept',
                'cta_url' => '#',
                'cta_open_new_tab' => 0,
                'cta_bg_color' => '#FFFFFF',
                'cta_text_color' => '#374151',
                'countdown_enabled' => 0,
                'is_dismissible' => 1,
                'cookie_duration' => 365,
                'animation' => 'fade_in',
                'auto_close_seconds' => 0,
                'store_ids' => '0',
                'customer_groups' => '',
                'page_targeting' => 'all',
                'show_on_mobile' => 1,
                'show_on_desktop' => 1,
            ],
            [
                'name' => 'Holiday Hours',
                'is_active' => 0,
                'bar_type' => 'warning',
                'position' => 'top_static',
                'sort_order' => 30,
                'content' => '⚠️ Holiday Notice: Shipping may be delayed during the holiday season. Order early!',
                'background_type' => 'color',
                'background_color' => '#92400E',
                'text_color' => '#FFFFFF',
                'font_size' => 14,
                'bar_height' => 0,
                'bar_padding' => '10px 20px',
                'icon' => 'warning',
                'cta_enabled' => 0,
                'countdown_enabled' => 0,
                'is_dismissible' => 1,
                'cookie_duration' => 7,
                'animation' => 'slide_down',
                'auto_close_seconds' => 0,
                'store_ids' => '0',
                'customer_groups' => '',
                'page_targeting' => 'all',
                'show_on_mobile' => 1,
                'show_on_desktop' => 1,
            ],
        ];

        $inserted = 0;
        foreach ($samples as $data) {
            try {
                $connection->insert($tableName, $data);
                $inserted++;
                $output->writeln('<info>Created: ' . $data['name'] . '</info>');
            } catch (\Exception $e) {
                $output->writeln('<error>Failed to create "' . $data['name'] . '": ' . $e->getMessage() . '</error>');
            }
        }

        $output->writeln('');
        $output->writeln('<info>Done! ' . $inserted . ' sample notification bar(s) created.</info>');
        $output->writeln('<comment>Run "bin/magento cache:flush" to see them on the storefront.</comment>');

        return Command::SUCCESS;
    }
}
