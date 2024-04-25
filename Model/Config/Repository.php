<?php
/**
 * Copyright © Magmodules.eu All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\MessageBird\Model\Config;

use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface as ScopeConfig;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface as StoreManager;
use Magmodules\MessageBird\Api\Config\RepositoryInterface;

/**
 * MessageBird Connect config repository class
 */
class Repository implements RepositoryInterface
{

    /**
     * @var ScopeConfig
     */
    private $scopeConfig;

    /**
     * @var StoreManager
     */
    private $storeManager;

    /**
     * @var ProductMetadataInterface
     */
    private $metadata;

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * Config repository constructor.
     *
     * @param ScopeConfig $scopeConfig
     * @param StoreManager $storeManager
     * @param ProductMetadataInterface $metadata
     */
    public function __construct(
        ScopeConfig $scopeConfig,
        StoreManager $storeManager,
        ProductMetadataInterface $metadata,
        EncryptorInterface $encryptor
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->metadata = $metadata;
        $this->encryptor = $encryptor;
    }

    /**
     * @inheritDoc
     */
    public function getExtensionCode(): string
    {
        return self::EXTENSION_CODE;
    }

    /**
     * {@inheritDoc}
     */

    public function getMagentoVersion(): string
    {
        return $this->metadata->getVersion();
    }

    /**
     * {@inheritDoc}
     */
    public function getExtensionVersion(): string
    {
        return $this->getStoreValue(self::XML_PATH_EXTENSION_VERSION);
    }

    /**
     * {@inheritDoc}
     */
    public function getStore(): StoreInterface
    {
        try {
            return $this->storeManager->getStore();
        } catch (Exception $e) {
            if ($store = $this->storeManager->getDefaultStoreView()) {
                return $store;
            }
        }
        return reset($this->storeManager->getStores());
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(int $storeId): bool
    {
        return $this->isSetFlag(self::ENABLED, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function getApiKey(int $storeId, ?bool $forceTestModus = null): string
    {
        $testModus = $forceTestModus === null ? $this->isSetFlag(self::TEST_MODE, $storeId) : $forceTestModus;
        if ($testModus) {
            return $this->encryptor->decrypt($this->getStoreValue(self::TEST_KEY, $storeId));
        }
        return $this->encryptor->decrypt($this->getStoreValue(self::LIVE_KEY, $storeId));
    }

    /**
     * @inheritDoc
     */
    public function getSender(int $storeId): string
    {
        return $this->getStoreValue(self::SENDER, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function isEnabledForOrders(int $storeId): bool
    {
        return $this->isSetFlag(self::ORDER, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function getOrderMessage(int $storeId): string
    {
        return $this->getStoreValue(self::ORDER_MESSAGE, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function isEnabledForInvoice(int $storeId): bool
    {
        return $this->isSetFlag(self::INVOICE, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function getInvoiceMessage(int $storeId): string
    {
        return $this->getStoreValue(self::INVOICE_MESSAGE, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function isEnabledForShipping(int $storeId): bool
    {
        return $this->isSetFlag(self::SHIPPING, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function getShippingMessage(int $storeId): string
    {
        return $this->getStoreValue(self::SHIPPING_MESSAGE, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function isEnabledForCredit(int $storeId): bool
    {
        return $this->isSetFlag(self::CREDIT, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function getCreditMessage(int $storeId): string
    {
        return $this->getStoreValue(self::CREDIT_MESSAGE, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function getOriginator(int $storeId): string
    {
        return $this->getStoreValue(self::ORIGINATOR, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function isEnabledForOutOfStock(int $storeId): bool
    {
        return $this->isSetFlag(self::OUT_OF_STOCK, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function getOutOfStockMessage(int $storeId): string
    {
        return $this->getStoreValue(self::OUT_OF_STOCK_MESSAGE, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function isEnabledForPriceChanges(int $storeId): bool
    {
        return $this->isSetFlag(self::PRICE_CHANGES, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function getPriceChangesMessage(int $storeId): string
    {
        return $this->getStoreValue(self::PRICE_CHANGES_MESSAGE, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function isEnabledForPaymentReminder(int $storeId): bool
    {
        return $this->isSetFlag(self::PAYMENT_REMINDER, $storeId)
            && $this->isSetFlag(self::MOLLIE_STATUS, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function getPaymentReminderMessage(int $storeId): string
    {
        return $this->getStoreValue(self::PAYMENT_REMINDER_MESSAGE, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function getRestrictionGeneral(int $storeId): array
    {
        $value = $this->getStoreValue(self::RESTRICTIONS_GENERAL, $storeId);
        if (!is_array($value)) {
            return explode(',', $value);
        }
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getRestrictionOrder(int $storeId): array
    {
        $value = $this->getStoreValue(self::RESTRICTIONS_ORDER, $storeId);
        if (!is_array($value)) {
            return explode(',', $value);
        }
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getRestrictionInvoice(int $storeId): array
    {
        $value = $this->getStoreValue(self::RESTRICTIONS_INVOICE, $storeId);
        if (!is_array($value)) {
            return explode(',', $value);
        }
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getRestrictionCreditmemo(int $storeId): array
    {
        $value = $this->getStoreValue(self::RESTRICTIONS_CREDITMEMO, $storeId);
        if (!is_array($value)) {
            return explode(',', $value);
        }
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getRestrictionShipment(int $storeId): array
    {
        $value = $this->getStoreValue(self::RESTRICTIONS_SHIPMENT, $storeId);
        if (!is_array($value)) {
            return explode(',', $value);
        }
        return [];
    }

    /**
     * Get config value flag
     *
     * @param string $path
     * @param int|null $storeId
     * @param string|null $scope
     *
     * @return bool
     */
    private function isSetFlag(string $path, int $storeId = null, string $scope = null): bool
    {
        if (empty($scope)) {
            $scope = ScopeInterface::SCOPE_STORE;
        }

        if (empty($storeId)) {
            $storeId = $this->getStore()->getId();
        }
        return $this->scopeConfig->isSetFlag($path, $scope, $storeId);
    }

    /**
     * Get Configuration data
     *
     * @param string $path
     * @param int|null $storeId
     * @param string|null $scope
     *
     * @return string
     */
    private function getStoreValue(
        string $path,
        int $storeId = null,
        string $scope = null
    ): string {
        if (!$storeId) {
            $storeId = (int)$this->getStore()->getId();
        }
        $scope = $scope ?? ScopeInterface::SCOPE_STORE;
        return (string)$this->scopeConfig->getValue($path, $scope, (int)$storeId);
    }

    /**
     * @inheritDoc
     */
    public function isDebugEnabled(int $storeId = null): bool
    {
        return $this->isSetFlag(self::DEBUG, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function isMollieEnabled(int $storeId = null): bool
    {
        return $this->isSetFlag(self::MOLLIE_STATUS, $storeId);
    }
}
