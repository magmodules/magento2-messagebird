<?php
/**
 * Copyright © Magmodules.eu All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magmodules\MessageBird\Api\Config;

/**
 * Interface Repository
 *
 */
interface RepositoryInterface
{

    const EXTENSION_CODE = 'Magmodules_MessageBird';
    const XML_PATH_EXTENSION_VERSION = 'magmodules_messagebird/general/version';
    const ENABLED = 'magmodules_messagebird/general/enable';
    const LIVE_KEY = 'magmodules_messagebird/general/api_key_live';
    const TEST_KEY = 'magmodules_messagebird/general/api_key_test';
    const ORIGINATOR = 'magmodules_messagebird/general/originator';
    const TEST_MODE = 'magmodules_messagebird/general/test_mode';
    const SENDER = 'magmodules_messagebird/general/sendername';

    const ORDER = 'magmodules_messagebird/communication/order';
    const ORDER_MESSAGE = 'magmodules_messagebird/communication/order_message';

    const INVOICE = 'magmodules_messagebird/communication/invoice';
    const INVOICE_MESSAGE = 'magmodules_messagebird/communication/invoice_message';

    const SHIPPING = 'magmodules_messagebird/communication/shipment';
    const SHIPPING_MESSAGE = 'magmodules_messagebird/communication/shipment_message';

    const CREDIT = 'magmodules_messagebird/communication/credit';
    const CREDIT_MESSAGE = 'magmodules_messagebird/communication/credit_message';

    const OUT_OF_STOCK = 'catalog/productalert/allow_stock';
    const OUT_OF_STOCK_MESSAGE = 'magmodules_messagebird/communication/out_of_stock_message';

    const PRICE_CHANGES = 'catalog/productalert/allow_price';
    const PRICE_CHANGES_MESSAGE = 'magmodules_messagebird/communication/price_changes_message';

    const PAYMENT_REMINDER = 'magmodules_messagebird/communication/payment_reminder';
    const PAYMENT_REMINDER_MESSAGE = 'magmodules_messagebird/communication/payment_reminder_message';
    const MOLLIE_STATUS = 'payment/mollie_general/enabled';

    const RESTRICTIONS_GENERAL = 'magmodules_messagebird/restrictions/general';
    const RESTRICTIONS_ORDER = 'magmodules_messagebird/restrictions/order';
    const RESTRICTIONS_INVOICE = 'magmodules_messagebird/restrictions/invoice';
    const RESTRICTIONS_CREDITMEMO = 'magmodules_messagebird/restrictions/creditmemo';
    const RESTRICTIONS_SHIPMENT = 'magmodules_messagebird/restrictions/shipment';

    const DEBUG = 'magmodules_messagebird/debug/debug';

    /**
     * Get extension version
     *
     * @return string
     */
    public function getExtensionVersion(): string;

    /**
     * Get extension code
     *
     * @return string
     */
    public function getExtensionCode(): string;

    /**
     * Get Magento Version
     *
     * @return string
     */
    public function getMagentoVersion(): string;

    /**
     * @param int $storeId
     * @return bool
     */
    public function isEnabled(int $storeId): bool;

    /**
     * @param int $storeId
     * @param null|bool $forceTestModus
     * @return string
     */
    public function getApiKey(int $storeId, ?bool $forceTestModus = null): string;

    /**
     * @param int $storeId
     * @return string
     */
    public function getSender(int $storeId): string;

    /**
     * @param int $storeId
     * @return string
     */
    public function getOriginator(int $storeId): string;

    /**
     * @param int $storeId
     * @return bool
     */
    public function isEnabledForOrders(int $storeId): bool;

    /**
     * @param int $storeId
     * @return string
     */
    public function getOrderMessage(int $storeId): string;

    /**
     * @param int $storeId
     * @return bool
     */
    public function isEnabledForInvoice(int $storeId): bool;

    /**
     * @param int $storeId
     * @return string
     */
    public function getInvoiceMessage(int $storeId): string;

    /**
     * @param int $storeId
     * @return bool
     */
    public function isEnabledForShipping(int $storeId): bool;

    /**
     * @param int $storeId
     * @return string
     */
    public function getShippingMessage(int $storeId): string;

    /**
     * @param int $storeId
     * @return bool
     */
    public function isEnabledForCredit(int $storeId): bool;

    /**
     * @param int $storeId
     * @return string
     */
    public function getCreditMessage(int $storeId): string;

    /**
     * @param int $storeId
     * @return bool
     */
    public function isEnabledForOutOfStock(int $storeId): bool;

    /**
     * @param int $storeId
     * @return string
     */
    public function getOutOfStockMessage(int $storeId): string;

    /**
     * @param int $storeId
     * @return bool
     */
    public function isEnabledForPriceChanges(int $storeId): bool;

    /**
     * @param int $storeId
     * @return string
     */
    public function getPriceChangesMessage(int $storeId): string;

    /**
     * @param int $storeId
     * @return bool
     */
    public function isEnabledForPaymentReminder(int $storeId): bool;

    /**
     * @param int $storeId
     * @return string
     */
    public function getPaymentReminderMessage(int $storeId): string;

    /**
     * @param int $storeId
     * @return array
     */
    public function getRestrictionGeneral(int $storeId): array;

    /**
     * @param int $storeId
     * @return array
     */
    public function getRestrictionOrder(int $storeId): array;

    /**
     * @param int $storeId
     * @return array
     */
    public function getRestrictionInvoice(int $storeId): array;

    /**
     * @param int $storeId
     * @return array
     */
    public function getRestrictionCreditmemo(int $storeId): array;

    /**
     * @param int $storeId
     * @return array
     */
    public function getRestrictionShipment(int $storeId): array;

    /**
     * @param int $storeId
     * @return bool
     */
    public function isDebugEnabled(int $storeId = null): bool;

    /**
     * @param int $storeId
     * @return bool
     */
    public function isMollieEnabled(int $storeId = null): bool;
}
