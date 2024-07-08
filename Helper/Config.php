<?php declare(strict_types=1);

namespace Magify\SlackNotifier\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;

class Config extends AbstractHelper
{
    const XML_PATH_SLACK_NOTIFIER_STATUS = 'dev/slack_notifier/enable';
    const XML_PATH_LOGGER_TYPES = 'dev/slack_notifier/logger_type';
    const XML_PATH_CHANNEL_ID = 'dev/slack_notifier/channel_id';
    const XML_PATH_TOKEN = 'dev/slack_notifier/token';
    const XML_PATH_IS_ASYNC = 'dev/slack_notifier/is_async';
    const XML_PATH_TIMEOUT = 'dev/slack_notifier/timeout';
    const XML_PATH_API_REQUEST_URI = 'dev/slack_notifier/url';
    private $storeManager;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
    )
    {
        parent::__construct($context);
        $this->storeManager = $storeManager;
    }

    /**
     * @param $path
     * @param $scopeType
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getConfig($path, $scopeType = ScopeInterface::SCOPE_STORE)
    {
        $scopeCode = $this->storeManager->getStore()->getId();
        return $this->scopeConfig->getValue($path, $scopeType, $scopeCode);
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isSlackNotifierEnabled(): int
    {
        return (int) $this->getConfig(self::XML_PATH_SLACK_NOTIFIER_STATUS);
    }

    /**
     * @return array
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
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getChannelId(): string
    {
        return $this->getConfig(self::XML_PATH_CHANNEL_ID);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getToken(): string
    {
        return $this->getConfig(self::XML_PATH_TOKEN);
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isSendAsync(): int
    {
        return (int) $this->getConfig(self::XML_PATH_IS_ASYNC);
    }

    /**
     * @return float
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getTimeout(): float
    {
        return (float) $this->getConfig(self::XML_PATH_TIMEOUT);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getApiUri(): string
    {
        return (string) $this->getConfig(self::XML_PATH_API_REQUEST_URI);
    }
}
