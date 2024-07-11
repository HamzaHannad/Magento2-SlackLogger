<?php

declare(strict_types=1);

namespace Magify\SlackNotifier\Helper;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Encryption\EncryptorInterface;

class Config extends AbstractHelper
{
    public const XML_PATH_SLACK_NOTIFIER_STATUS = 'dev/slack_notifier/enable';
    public const XML_PATH_LOGGER_TYPES = 'dev/slack_notifier/logger_type';
    public const XML_PATH_CHANNEL_ID = 'dev/slack_notifier/channel_id';
    public const XML_PATH_TOKEN = 'dev/slack_notifier/token';
    public const XML_PATH_IS_ASYNC = 'dev/slack_notifier/is_async';
    public const XML_PATH_TIMEOUT = 'dev/slack_notifier/timeout';
    public const XML_PATH_API_REQUEST_URI = 'dev/slack_notifier/url';
    private $storeManager;
    private $encryptor;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        EncryptorInterface $encryptor,
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->encryptor = $encryptor;
    }

    /**
     * @param $path
     * @param string $scopeType
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getConfig($path, string $scopeType = ScopeInterface::SCOPE_STORE): mixed
    {
        $scopeCode = $this->storeManager->getStore()->getId();
        return $this->scopeConfig->getValue($path, $scopeType, $scopeCode);
    }

    /**
     * @return int
     * @throws NoSuchEntityException
     */
    public function isSlackNotifierEnabled(): int
    {
        return (int) $this->getConfig(self::XML_PATH_SLACK_NOTIFIER_STATUS);
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    public function getLoggerTypes(): array
    {
        if ($type = $this->getConfig(self::XML_PATH_LOGGER_TYPES)) {
            return explode(',', $type);
        }

        return [];
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getChannelId(): string
    {
        return $this->getConfig(self::XML_PATH_CHANNEL_ID);
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getToken(): string
    {
        $token =  $this->getConfig(self::XML_PATH_TOKEN);
        return $this->encryptor->decrypt($token);
    }

    /**
     * @return int
     * @throws NoSuchEntityException
     */
    public function isSendAsync(): int
    {
        return (int) $this->getConfig(self::XML_PATH_IS_ASYNC);
    }

    /**
     * @return float
     * @throws NoSuchEntityException
     */
    public function getTimeout(): float
    {
        return (float) $this->getConfig(self::XML_PATH_TIMEOUT);
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getApiUri(): string
    {
        return (string) $this->getConfig(self::XML_PATH_API_REQUEST_URI);
    }
}
