<?php

declare(strict_types=1);

namespace Panth\NotificationBar\Model;

use Magento\Framework\Model\AbstractModel;
use Panth\NotificationBar\Model\ResourceModel\Bar as BarResource;

class Bar extends AbstractModel
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'panth_notification_bar';

    /**
     * @inheritdoc
     */
    protected function _construct(): void
    {
        $this->_init(BarResource::class);
    }

    /**
     * Get bar ID
     *
     * @return int|null
     */
    public function getBarId(): ?int
    {
        $value = $this->getData('bar_id');
        return $value !== null ? (int)$value : null;
    }

    /**
     * Set bar ID
     *
     * @param int $barId
     * @return $this
     */
    public function setBarId(int $barId): self
    {
        return $this->setData('bar_id', $barId);
    }

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->getData('name');
    }

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        return $this->setData('name', $name);
    }

    /**
     * Get is active
     *
     * @return bool
     */
    public function getIsActive(): bool
    {
        return (bool)$this->getData('is_active');
    }

    /**
     * Set is active
     *
     * @param bool $isActive
     * @return $this
     */
    public function setIsActive(bool $isActive): self
    {
        return $this->setData('is_active', $isActive);
    }

    /**
     * Get bar type
     *
     * @return string|null
     */
    public function getBarType(): ?string
    {
        return $this->getData('bar_type');
    }

    /**
     * Set bar type
     *
     * @param string $barType
     * @return $this
     */
    public function setBarType(string $barType): self
    {
        return $this->setData('bar_type', $barType);
    }

    /**
     * Get position
     *
     * @return string|null
     */
    public function getPosition(): ?string
    {
        return $this->getData('position');
    }

    /**
     * Set position
     *
     * @param string $position
     * @return $this
     */
    public function setPosition(string $position): self
    {
        return $this->setData('position', $position);
    }

    /**
     * Get sort order
     *
     * @return int|null
     */
    public function getSortOrder(): ?int
    {
        $value = $this->getData('sort_order');
        return $value !== null ? (int)$value : null;
    }

    /**
     * Set sort order
     *
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder(int $sortOrder): self
    {
        return $this->setData('sort_order', $sortOrder);
    }

    /**
     * Get content
     *
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->getData('content');
    }

    /**
     * Set content
     *
     * @param string $content
     * @return $this
     */
    public function setContent(string $content): self
    {
        return $this->setData('content', $content);
    }

    /**
     * Get background type
     *
     * @return string|null
     */
    public function getBackgroundType(): ?string
    {
        return $this->getData('background_type');
    }

    /**
     * Set background type
     *
     * @param string $backgroundType
     * @return $this
     */
    public function setBackgroundType(string $backgroundType): self
    {
        return $this->setData('background_type', $backgroundType);
    }

    /**
     * Get background color
     *
     * @return string|null
     */
    public function getBackgroundColor(): ?string
    {
        return $this->getData('background_color');
    }

    /**
     * Set background color
     *
     * @param string $backgroundColor
     * @return $this
     */
    public function setBackgroundColor(string $backgroundColor): self
    {
        return $this->setData('background_color', $backgroundColor);
    }

    /**
     * Get background gradient
     *
     * @return string|null
     */
    public function getBackgroundGradient(): ?string
    {
        return $this->getData('background_gradient');
    }

    /**
     * Set background gradient
     *
     * @param string|null $backgroundGradient
     * @return $this
     */
    public function setBackgroundGradient(?string $backgroundGradient): self
    {
        return $this->setData('background_gradient', $backgroundGradient);
    }

    /**
     * Get background image
     *
     * @return string|null
     */
    public function getBackgroundImage(): ?string
    {
        return $this->getData('background_image');
    }

    /**
     * Set background image
     *
     * @param string|null $backgroundImage
     * @return $this
     */
    public function setBackgroundImage(?string $backgroundImage): self
    {
        return $this->setData('background_image', $backgroundImage);
    }

    /**
     * Get text color
     *
     * @return string|null
     */
    public function getTextColor(): ?string
    {
        return $this->getData('text_color');
    }

    /**
     * Set text color
     *
     * @param string $textColor
     * @return $this
     */
    public function setTextColor(string $textColor): self
    {
        return $this->setData('text_color', $textColor);
    }

    /**
     * Get font size
     *
     * @return int|null
     */
    public function getFontSize(): ?int
    {
        $value = $this->getData('font_size');
        return $value !== null ? (int)$value : null;
    }

    /**
     * Set font size
     *
     * @param int $fontSize
     * @return $this
     */
    public function setFontSize(int $fontSize): self
    {
        return $this->setData('font_size', $fontSize);
    }

    /**
     * Get bar height
     *
     * @return int|null
     */
    public function getBarHeight(): ?int
    {
        $value = $this->getData('bar_height');
        return $value !== null ? (int)$value : null;
    }

    /**
     * Set bar height
     *
     * @param int $barHeight
     * @return $this
     */
    public function setBarHeight(int $barHeight): self
    {
        return $this->setData('bar_height', $barHeight);
    }

    /**
     * Get bar padding
     *
     * @return string|null
     */
    public function getBarPadding(): ?string
    {
        return $this->getData('bar_padding');
    }

    /**
     * Set bar padding
     *
     * @param string $barPadding
     * @return $this
     */
    public function setBarPadding(string $barPadding): self
    {
        return $this->setData('bar_padding', $barPadding);
    }

    /**
     * Get icon
     *
     * @return string|null
     */
    public function getIcon(): ?string
    {
        return $this->getData('icon');
    }

    /**
     * Set icon
     *
     * @param string|null $icon
     * @return $this
     */
    public function setIcon(?string $icon): self
    {
        return $this->setData('icon', $icon);
    }

    /**
     * Get custom CSS
     *
     * @return string|null
     */
    public function getCustomCss(): ?string
    {
        return $this->getData('custom_css');
    }

    /**
     * Set custom CSS
     *
     * @param string|null $customCss
     * @return $this
     */
    public function setCustomCss(?string $customCss): self
    {
        return $this->setData('custom_css', $customCss);
    }

    /**
     * Get CTA enabled
     *
     * @return bool
     */
    public function getCtaEnabled(): bool
    {
        return (bool)$this->getData('cta_enabled');
    }

    /**
     * Set CTA enabled
     *
     * @param bool $ctaEnabled
     * @return $this
     */
    public function setCtaEnabled(bool $ctaEnabled): self
    {
        return $this->setData('cta_enabled', $ctaEnabled);
    }

    /**
     * Get CTA text
     *
     * @return string|null
     */
    public function getCtaText(): ?string
    {
        return $this->getData('cta_text');
    }

    /**
     * Set CTA text
     *
     * @param string|null $ctaText
     * @return $this
     */
    public function setCtaText(?string $ctaText): self
    {
        return $this->setData('cta_text', $ctaText);
    }

    /**
     * Get CTA URL
     *
     * @return string|null
     */
    public function getCtaUrl(): ?string
    {
        return $this->getData('cta_url');
    }

    /**
     * Set CTA URL
     *
     * @param string|null $ctaUrl
     * @return $this
     */
    public function setCtaUrl(?string $ctaUrl): self
    {
        return $this->setData('cta_url', $ctaUrl);
    }

    /**
     * Get CTA open new tab
     *
     * @return bool
     */
    public function getCtaOpenNewTab(): bool
    {
        return (bool)$this->getData('cta_open_new_tab');
    }

    /**
     * Set CTA open new tab
     *
     * @param bool $ctaOpenNewTab
     * @return $this
     */
    public function setCtaOpenNewTab(bool $ctaOpenNewTab): self
    {
        return $this->setData('cta_open_new_tab', $ctaOpenNewTab);
    }

    /**
     * Get CTA background color
     *
     * @return string|null
     */
    public function getCtaBgColor(): ?string
    {
        return $this->getData('cta_bg_color');
    }

    /**
     * Set CTA background color
     *
     * @param string $ctaBgColor
     * @return $this
     */
    public function setCtaBgColor(string $ctaBgColor): self
    {
        return $this->setData('cta_bg_color', $ctaBgColor);
    }

    /**
     * Get CTA text color
     *
     * @return string|null
     */
    public function getCtaTextColor(): ?string
    {
        return $this->getData('cta_text_color');
    }

    /**
     * Set CTA text color
     *
     * @param string $ctaTextColor
     * @return $this
     */
    public function setCtaTextColor(string $ctaTextColor): self
    {
        return $this->setData('cta_text_color', $ctaTextColor);
    }

    /**
     * Get countdown enabled
     *
     * @return bool
     */
    public function getCountdownEnabled(): bool
    {
        return (bool)$this->getData('countdown_enabled');
    }

    /**
     * Set countdown enabled
     *
     * @param bool $countdownEnabled
     * @return $this
     */
    public function setCountdownEnabled(bool $countdownEnabled): self
    {
        return $this->setData('countdown_enabled', $countdownEnabled);
    }

    /**
     * Get countdown end date
     *
     * @return string|null
     */
    public function getCountdownEndDate(): ?string
    {
        return $this->getData('countdown_end_date');
    }

    /**
     * Set countdown end date
     *
     * @param string|null $countdownEndDate
     * @return $this
     */
    public function setCountdownEndDate(?string $countdownEndDate): self
    {
        return $this->setData('countdown_end_date', $countdownEndDate);
    }

    /**
     * Get countdown label
     *
     * @return string|null
     */
    public function getCountdownLabel(): ?string
    {
        return $this->getData('countdown_label');
    }

    /**
     * Set countdown label
     *
     * @param string|null $countdownLabel
     * @return $this
     */
    public function setCountdownLabel(?string $countdownLabel): self
    {
        return $this->setData('countdown_label', $countdownLabel);
    }

    /**
     * Get countdown expired text
     *
     * @return string|null
     */
    public function getCountdownExpiredText(): ?string
    {
        return $this->getData('countdown_expired_text');
    }

    /**
     * Set countdown expired text
     *
     * @param string|null $countdownExpiredText
     * @return $this
     */
    public function setCountdownExpiredText(?string $countdownExpiredText): self
    {
        return $this->setData('countdown_expired_text', $countdownExpiredText);
    }

    /**
     * Get is dismissible
     *
     * @return bool
     */
    public function getIsDismissible(): bool
    {
        return (bool)$this->getData('is_dismissible');
    }

    /**
     * Set is dismissible
     *
     * @param bool $isDismissible
     * @return $this
     */
    public function setIsDismissible(bool $isDismissible): self
    {
        return $this->setData('is_dismissible', $isDismissible);
    }

    /**
     * Get cookie duration
     *
     * @return int|null
     */
    public function getCookieDuration(): ?int
    {
        $value = $this->getData('cookie_duration');
        return $value !== null ? (int)$value : null;
    }

    /**
     * Set cookie duration
     *
     * @param int $cookieDuration
     * @return $this
     */
    public function setCookieDuration(int $cookieDuration): self
    {
        return $this->setData('cookie_duration', $cookieDuration);
    }

    /**
     * Get animation
     *
     * @return string|null
     */
    public function getAnimation(): ?string
    {
        return $this->getData('animation');
    }

    /**
     * Set animation
     *
     * @param string $animation
     * @return $this
     */
    public function setAnimation(string $animation): self
    {
        return $this->setData('animation', $animation);
    }

    /**
     * Get auto close seconds
     *
     * @return int|null
     */
    public function getAutoCloseSeconds(): ?int
    {
        $value = $this->getData('auto_close_seconds');
        return $value !== null ? (int)$value : null;
    }

    /**
     * Set auto close seconds
     *
     * @param int $autoCloseSeconds
     * @return $this
     */
    public function setAutoCloseSeconds(int $autoCloseSeconds): self
    {
        return $this->setData('auto_close_seconds', $autoCloseSeconds);
    }

    /**
     * Get date from
     *
     * @return string|null
     */
    public function getDateFrom(): ?string
    {
        return $this->getData('date_from');
    }

    /**
     * Set date from
     *
     * @param string|null $dateFrom
     * @return $this
     */
    public function setDateFrom(?string $dateFrom): self
    {
        return $this->setData('date_from', $dateFrom);
    }

    /**
     * Get date to
     *
     * @return string|null
     */
    public function getDateTo(): ?string
    {
        return $this->getData('date_to');
    }

    /**
     * Set date to
     *
     * @param string|null $dateTo
     * @return $this
     */
    public function setDateTo(?string $dateTo): self
    {
        return $this->setData('date_to', $dateTo);
    }

    /**
     * Get store IDs
     *
     * @return string|null
     */
    public function getStoreIds(): ?string
    {
        return $this->getData('store_ids');
    }

    /**
     * Set store IDs
     *
     * @param string|null $storeIds
     * @return $this
     */
    public function setStoreIds(?string $storeIds): self
    {
        return $this->setData('store_ids', $storeIds);
    }

    /**
     * Get customer groups
     *
     * @return string|null
     */
    public function getCustomerGroups(): ?string
    {
        return $this->getData('customer_groups');
    }

    /**
     * Set customer groups
     *
     * @param string|null $customerGroups
     * @return $this
     */
    public function setCustomerGroups(?string $customerGroups): self
    {
        return $this->setData('customer_groups', $customerGroups);
    }

    /**
     * Get page targeting
     *
     * @return string|null
     */
    public function getPageTargeting(): ?string
    {
        return $this->getData('page_targeting');
    }

    /**
     * Set page targeting
     *
     * @param string $pageTargeting
     * @return $this
     */
    public function setPageTargeting(string $pageTargeting): self
    {
        return $this->setData('page_targeting', $pageTargeting);
    }

    /**
     * Get target URLs
     *
     * @return string|null
     */
    public function getTargetUrls(): ?string
    {
        return $this->getData('target_urls');
    }

    /**
     * Set target URLs
     *
     * @param string|null $targetUrls
     * @return $this
     */
    public function setTargetUrls(?string $targetUrls): self
    {
        return $this->setData('target_urls', $targetUrls);
    }

    /**
     * Get target page types
     *
     * @return string|null
     */
    public function getTargetPageTypes(): ?string
    {
        return $this->getData('target_page_types');
    }

    /**
     * Set target page types
     *
     * @param string|null $targetPageTypes
     * @return $this
     */
    public function setTargetPageTypes(?string $targetPageTypes): self
    {
        return $this->setData('target_page_types', $targetPageTypes);
    }

    /**
     * Get target countries
     *
     * @return string|null
     */
    public function getTargetCountries(): ?string
    {
        return $this->getData('target_countries');
    }

    /**
     * Set target countries
     *
     * @param string|null $targetCountries
     * @return $this
     */
    public function setTargetCountries(?string $targetCountries): self
    {
        return $this->setData('target_countries', $targetCountries);
    }

    /**
     * Get target URL params
     *
     * @return string|null
     */
    public function getTargetUrlParams(): ?string
    {
        return $this->getData('target_url_params');
    }

    /**
     * Set target URL params
     *
     * @param string|null $targetUrlParams
     * @return $this
     */
    public function setTargetUrlParams(?string $targetUrlParams): self
    {
        return $this->setData('target_url_params', $targetUrlParams);
    }

    /**
     * Get show on mobile
     *
     * @return bool
     */
    public function getShowOnMobile(): bool
    {
        return (bool)$this->getData('show_on_mobile');
    }

    /**
     * Set show on mobile
     *
     * @param bool $showOnMobile
     * @return $this
     */
    public function setShowOnMobile(bool $showOnMobile): self
    {
        return $this->setData('show_on_mobile', $showOnMobile);
    }

    /**
     * Get show on desktop
     *
     * @return bool
     */
    public function getShowOnDesktop(): bool
    {
        return (bool)$this->getData('show_on_desktop');
    }

    /**
     * Set show on desktop
     *
     * @param bool $showOnDesktop
     * @return $this
     */
    public function setShowOnDesktop(bool $showOnDesktop): self
    {
        return $this->setData('show_on_desktop', $showOnDesktop);
    }

    /**
     * Get mobile content
     *
     * @return string|null
     */
    public function getMobileContent(): ?string
    {
        return $this->getData('mobile_content');
    }

    /**
     * Set mobile content
     *
     * @param string|null $mobileContent
     * @return $this
     */
    public function setMobileContent(?string $mobileContent): self
    {
        return $this->setData('mobile_content', $mobileContent);
    }

    /**
     * Get created at
     *
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->getData('created_at');
    }

    /**
     * Set created at
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt(string $createdAt): self
    {
        return $this->setData('created_at', $createdAt);
    }

    /**
     * Get updated at
     *
     * @return string|null
     */
    public function getUpdatedAt(): ?string
    {
        return $this->getData('updated_at');
    }

    /**
     * Set updated at
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt(string $updatedAt): self
    {
        return $this->setData('updated_at', $updatedAt);
    }

    /**
     * Get store IDs as array
     *
     * @return array
     */
    public function getStoreIdsArray(): array
    {
        $value = $this->getStoreIds();
        return $value ? array_filter(explode(',', $value)) : [];
    }

    /**
     * Get customer groups as array
     *
     * @return array
     */
    public function getCustomerGroupsArray(): array
    {
        $value = $this->getCustomerGroups();
        return $value ? array_filter(explode(',', $value)) : [];
    }

    /**
     * Get target countries as array
     *
     * @return array
     */
    public function getTargetCountriesArray(): array
    {
        $value = $this->getTargetCountries();
        return $value ? array_filter(explode(',', $value)) : [];
    }

    /**
     * Get target page types as array
     *
     * @return array
     */
    public function getTargetPageTypesArray(): array
    {
        $value = $this->getTargetPageTypes();
        return $value ? array_filter(explode(',', $value)) : [];
    }

    /**
     * Get target URLs as array
     *
     * @return array
     */
    public function getTargetUrlsArray(): array
    {
        $value = $this->getTargetUrls();
        return $value ? array_filter(explode(',', $value)) : [];
    }
}
