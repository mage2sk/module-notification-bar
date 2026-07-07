<?php
declare(strict_types=1);

namespace Panth\NotificationBar\Model;

use Magento\Framework\Model\AbstractModel;
use Panth\NotificationBar\Model\ResourceModel\Bar as BarResource;

class Bar extends AbstractModel
{
    protected $_eventPrefix = 'panth_notification_bar';

    protected function _construct(): void
    {
        $this->_init(BarResource::class);
    }

    public function getBarId(): ?int
    {
        $value = $this->getData('bar_id');
        return $value !== null ? (int)$value : null;
    }

    public function setBarId(int $barId): self
    {
        return $this->setData('bar_id', $barId);
    }

    public function getName(): ?string
    {
        return $this->getData('name');
    }

    public function setName(string $name): self
    {
        return $this->setData('name', $name);
    }

    public function getIsActive(): bool
    {
        return (bool)$this->getData('is_active');
    }

    public function setIsActive(bool $isActive): self
    {
        return $this->setData('is_active', $isActive);
    }

    public function getBarType(): ?string
    {
        return $this->getData('bar_type');
    }

    public function setBarType(string $barType): self
    {
        return $this->setData('bar_type', $barType);
    }

    public function getPosition(): ?string
    {
        return $this->getData('position');
    }

    public function setPosition(string $position): self
    {
        return $this->setData('position', $position);
    }

    public function getSortOrder(): ?int
    {
        $value = $this->getData('sort_order');
        return $value !== null ? (int)$value : null;
    }

    public function setSortOrder(int $sortOrder): self
    {
        return $this->setData('sort_order', $sortOrder);
    }

    public function getContent(): ?string
    {
        return $this->getData('content');
    }

    public function setContent(string $content): self
    {
        return $this->setData('content', $content);
    }

    public function getBackgroundType(): ?string
    {
        return $this->getData('background_type');
    }

    public function setBackgroundType(string $backgroundType): self
    {
        return $this->setData('background_type', $backgroundType);
    }

    public function getBackgroundColor(): ?string
    {
        return $this->getData('background_color');
    }

    public function setBackgroundColor(string $backgroundColor): self
    {
        return $this->setData('background_color', $backgroundColor);
    }

    public function getBackgroundGradient(): ?string
    {
        return $this->getData('background_gradient');
    }

    public function setBackgroundGradient(?string $backgroundGradient): self
    {
        return $this->setData('background_gradient', $backgroundGradient);
    }

    public function getBackgroundImage(): ?string
    {
        return $this->getData('background_image');
    }

    public function setBackgroundImage(?string $backgroundImage): self
    {
        return $this->setData('background_image', $backgroundImage);
    }

    public function getTextColor(): ?string
    {
        return $this->getData('text_color');
    }

    public function setTextColor(string $textColor): self
    {
        return $this->setData('text_color', $textColor);
    }

    public function getFontSize(): ?int
    {
        $value = $this->getData('font_size');
        return $value !== null ? (int)$value : null;
    }

    public function setFontSize(int $fontSize): self
    {
        return $this->setData('font_size', $fontSize);
    }

    public function getBarHeight(): ?int
    {
        $value = $this->getData('bar_height');
        return $value !== null ? (int)$value : null;
    }

    public function setBarHeight(int $barHeight): self
    {
        return $this->setData('bar_height', $barHeight);
    }

    public function getBarPadding(): ?string
    {
        return $this->getData('bar_padding');
    }

    public function setBarPadding(string $barPadding): self
    {
        return $this->setData('bar_padding', $barPadding);
    }

    public function getIcon(): ?string
    {
        return $this->getData('icon');
    }

    public function setIcon(?string $icon): self
    {
        return $this->setData('icon', $icon);
    }

    public function getCustomCss(): ?string
    {
        return $this->getData('custom_css');
    }

    public function setCustomCss(?string $customCss): self
    {
        return $this->setData('custom_css', $customCss);
    }

    public function getCtaEnabled(): bool
    {
        return (bool)$this->getData('cta_enabled');
    }

    public function setCtaEnabled(bool $ctaEnabled): self
    {
        return $this->setData('cta_enabled', $ctaEnabled);
    }

    public function getCtaText(): ?string
    {
        return $this->getData('cta_text');
    }

    public function setCtaText(?string $ctaText): self
    {
        return $this->setData('cta_text', $ctaText);
    }

    public function getCtaUrl(): ?string
    {
        return $this->getData('cta_url');
    }

    public function setCtaUrl(?string $ctaUrl): self
    {
        return $this->setData('cta_url', $ctaUrl);
    }

    public function getCtaOpenNewTab(): bool
    {
        return (bool)$this->getData('cta_open_new_tab');
    }

    public function setCtaOpenNewTab(bool $ctaOpenNewTab): self
    {
        return $this->setData('cta_open_new_tab', $ctaOpenNewTab);
    }

    public function getCtaBgColor(): ?string
    {
        return $this->getData('cta_bg_color');
    }

    public function setCtaBgColor(string $ctaBgColor): self
    {
        return $this->setData('cta_bg_color', $ctaBgColor);
    }

    public function getCtaTextColor(): ?string
    {
        return $this->getData('cta_text_color');
    }

    public function setCtaTextColor(string $ctaTextColor): self
    {
        return $this->setData('cta_text_color', $ctaTextColor);
    }

    public function getCountdownEnabled(): bool
    {
        return (bool)$this->getData('countdown_enabled');
    }

    public function setCountdownEnabled(bool $countdownEnabled): self
    {
        return $this->setData('countdown_enabled', $countdownEnabled);
    }

    public function getCountdownEndDate(): ?string
    {
        return $this->getData('countdown_end_date');
    }

    public function setCountdownEndDate(?string $countdownEndDate): self
    {
        return $this->setData('countdown_end_date', $countdownEndDate);
    }

    public function getCountdownLabel(): ?string
    {
        return $this->getData('countdown_label');
    }

    public function setCountdownLabel(?string $countdownLabel): self
    {
        return $this->setData('countdown_label', $countdownLabel);
    }

    public function getCountdownExpiredText(): ?string
    {
        return $this->getData('countdown_expired_text');
    }

    public function setCountdownExpiredText(?string $countdownExpiredText): self
    {
        return $this->setData('countdown_expired_text', $countdownExpiredText);
    }

    public function getIsDismissible(): bool
    {
        return (bool)$this->getData('is_dismissible');
    }

    public function setIsDismissible(bool $isDismissible): self
    {
        return $this->setData('is_dismissible', $isDismissible);
    }

    public function getCookieDuration(): ?int
    {
        $value = $this->getData('cookie_duration');
        return $value !== null ? (int)$value : null;
    }

    public function setCookieDuration(int $cookieDuration): self
    {
        return $this->setData('cookie_duration', $cookieDuration);
    }

    public function getAnimation(): ?string
    {
        return $this->getData('animation');
    }

    public function setAnimation(string $animation): self
    {
        return $this->setData('animation', $animation);
    }

    public function getAutoCloseSeconds(): ?int
    {
        $value = $this->getData('auto_close_seconds');
        return $value !== null ? (int)$value : null;
    }

    public function setAutoCloseSeconds(int $autoCloseSeconds): self
    {
        return $this->setData('auto_close_seconds', $autoCloseSeconds);
    }

    public function getDateFrom(): ?string
    {
        return $this->getData('date_from');
    }

    public function setDateFrom(?string $dateFrom): self
    {
        return $this->setData('date_from', $dateFrom);
    }

    public function getDateTo(): ?string
    {
        return $this->getData('date_to');
    }

    public function setDateTo(?string $dateTo): self
    {
        return $this->setData('date_to', $dateTo);
    }

    public function getStoreIds(): ?string
    {
        return $this->getData('store_ids');
    }

    public function setStoreIds(?string $storeIds): self
    {
        return $this->setData('store_ids', $storeIds);
    }

    public function getCustomerGroups(): ?string
    {
        return $this->getData('customer_groups');
    }

    public function setCustomerGroups(?string $customerGroups): self
    {
        return $this->setData('customer_groups', $customerGroups);
    }

    public function getPageTargeting(): ?string
    {
        return $this->getData('page_targeting');
    }

    public function setPageTargeting(string $pageTargeting): self
    {
        return $this->setData('page_targeting', $pageTargeting);
    }

    public function getTargetUrls(): ?string
    {
        return $this->getData('target_urls');
    }

    public function setTargetUrls(?string $targetUrls): self
    {
        return $this->setData('target_urls', $targetUrls);
    }

    public function getTargetPageTypes(): ?string
    {
        return $this->getData('target_page_types');
    }

    public function setTargetPageTypes(?string $targetPageTypes): self
    {
        return $this->setData('target_page_types', $targetPageTypes);
    }

    public function getTargetCountries(): ?string
    {
        return $this->getData('target_countries');
    }

    public function setTargetCountries(?string $targetCountries): self
    {
        return $this->setData('target_countries', $targetCountries);
    }

    public function getTargetUrlParams(): ?string
    {
        return $this->getData('target_url_params');
    }

    public function setTargetUrlParams(?string $targetUrlParams): self
    {
        return $this->setData('target_url_params', $targetUrlParams);
    }

    public function getShowOnMobile(): bool
    {
        return (bool)$this->getData('show_on_mobile');
    }

    public function setShowOnMobile(bool $showOnMobile): self
    {
        return $this->setData('show_on_mobile', $showOnMobile);
    }

    public function getShowOnDesktop(): bool
    {
        return (bool)$this->getData('show_on_desktop');
    }

    public function setShowOnDesktop(bool $showOnDesktop): self
    {
        return $this->setData('show_on_desktop', $showOnDesktop);
    }

    public function getMobileContent(): ?string
    {
        return $this->getData('mobile_content');
    }

    public function setMobileContent(?string $mobileContent): self
    {
        return $this->setData('mobile_content', $mobileContent);
    }

    public function getCreatedAt(): ?string
    {
        return $this->getData('created_at');
    }

    public function setCreatedAt(string $createdAt): self
    {
        return $this->setData('created_at', $createdAt);
    }

    public function getUpdatedAt(): ?string
    {
        return $this->getData('updated_at');
    }

    public function setUpdatedAt(string $updatedAt): self
    {
        return $this->setData('updated_at', $updatedAt);
    }

    public function getStoreIdsArray(): array
    {
        $value = $this->getStoreIds();
        return $value ? array_filter(explode(',', $value)) : [];
    }

    public function getCustomerGroupsArray(): array
    {
        $value = $this->getCustomerGroups();
        return $value ? array_filter(explode(',', $value)) : [];
    }

    public function getTargetCountriesArray(): array
    {
        $value = $this->getTargetCountries();
        return $value ? array_filter(explode(',', $value)) : [];
    }

    public function getTargetPageTypesArray(): array
    {
        $value = $this->getTargetPageTypes();
        return $value ? array_filter(explode(',', $value)) : [];
    }

    public function getTargetUrlsArray(): array
    {
        $value = $this->getTargetUrls();
        return $value ? array_filter(explode(',', $value)) : [];
    }
}
