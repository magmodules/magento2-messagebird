<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magmodules\MessageBird\Service\Api;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use Magento\Framework\Serialize\Serializer\Json;
use MessageBird\Client;
use Magmodules\MessageBird\Api\Config\RepositoryInterface as ConfigRepository;
use Magmodules\MessageBird\Api\Log\RepositoryInterface as LogRepository;
use MessageBird\Objects\Message;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Service class for API adapter
 */
class Adapter
{

    /**
     * Error key value pair
     */
    const ERRORS = [
        'incorrect access_key' => 'Incorrect access key',
        'no (correct) recipients found' => 'Originator is invalid',
        'phone number has unknown format' => 'Phone number has unknown format: ',
        'unknown' => 'Unknown error'
    ];

    /**
     * Country path
     */
    const COUNTRY_CODE_PATH = 'general/store_information/country_id';

    /**
     * @var Message
     */
    private $message;

    /**
     * @var ConfigRepository
     */
    private $configRepository;

    /**
     * @var LogRepository
     */
    private $logRepository;

    /**
     * @var Json
     */
    private $json;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Adapter constructor.
     *
     * @param Message $message
     * @param ConfigRepository $configRepository
     * @param LogRepository $logRepository
     * @param Json $json
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Message $message,
        ConfigRepository $configRepository,
        LogRepository $logRepository,
        Json $json,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->message = $message;
        $this->configRepository = $configRepository;
        $this->logRepository = $logRepository;
        $this->json = $json;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param array $config
     * @param int $storeId
     * @return array
     */
    public function execute(array $config, int $storeId = 0): array
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        $messagebird = new Client($config['access_key']);

        if (!isset($config['recipients'])) {
            $config['recipients'] = [$config['originator']];
            $config['message_body'] = 'Credentials check';
        }
        if (!is_array($config['recipients'])) {
            $config['recipients'] = [$config['recipients']];
        }
        if (!isset($config['country_code'])) {
            $countryCode = $this->scopeConfig->getValue(
                self::COUNTRY_CODE_PATH,
                ScopeInterface::SCOPE_STORE,
                $storeId
            );
        } else {
            $countryCode = $config['country_code'];
        }

        $originator = $config['originator'];
        $originator = preg_replace('/[^0-9.]+/', '', $originator);
        try {
            $numberProto = $phoneUtil->parse($originator, $countryCode);
            $originator = sprintf(
                '+%s%s',
                $numberProto->getCountryCode(),
                $numberProto->getNationalNumber()
            );
        } catch (NumberParseException $exception) {
            return [
                'success' => false,
                'message' => $exception->getMessage()
            ];
        }
        try {
            $messagebird->lookup->read($originator);
        } catch (\Exception $e) {
            return $this->processError($e->getMessage());
        }
        $this->message->originator = $originator;
        foreach ($config['recipients'] as &$recipient) {
            $recipient = preg_replace('/[^0-9.]+/', '', $recipient);
            try {
                $numberProto = $phoneUtil->parse($recipient, $countryCode);
                $recipient = sprintf(
                    '+%s%s',
                    $numberProto->getCountryCode(),
                    $numberProto->getNationalNumber()
                );
            } catch (NumberParseException $exception) {
                return [
                    'success' => false,
                    'message' => $exception->getMessage()
                ];
            }
            try {
                $messagebird->lookup->read($recipient);
            } catch (\Exception $e) {
                return $this->processError($e->getMessage());
            }
        }
        $this->message->recipients = $config['recipients'];
        $this->message->body = $config['message_body'];
        if ($this->configRepository->isDebugEnabled()) {
            $this->logRepository->addDebugLog('RequestData', $this->json->serialize($config));
        }
        try {
            $result = $messagebird->messages->create($this->message);
            if ($this->configRepository->isDebugEnabled()) {
                $this->logRepository->addDebugLog('ResponseData', $this->json->serialize($result));
            }
        } catch (\Exception $e) {
            return $this->processError($e->getMessage());
        }
        return [
            'success' => true,
            'message' => ''
        ];
    }

    /**
     * @param string $message
     * @return array
     */
    private function processError(string $message, string $extra = ''): array
    {
        foreach (self::ERRORS as $key => $test) {
            if (strpos($message, $key) !== false) {
                $this->logRepository->addErrorLog('Request error', self::ERRORS[$key] . $extra);
                return [
                    'success' => false,
                    'message' => self::ERRORS[$key] . $extra
                ];
            }
        }
        $this->logRepository->addErrorLog('Request error', self::ERRORS['unknown']);
        return [
            'success' => false,
            'message' => self::ERRORS['unknown']
        ];
    }
}
