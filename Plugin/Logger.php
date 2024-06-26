<?php declare(strict_types=1);

namespace Magify\SlackNotifier\Plugin;

use Magento\Framework\DataObject;
use Magify\SlackNotifier\Helper\Message as MessageHelper;
use Monolog\Logger as MonologLogger;
use Magify\SlackNotifier\Helper\Config as ConfigHelper;

class Logger
{
    private $configHelper;
    private $messageHelper;
    private $dataObject;

    /**
     * @param ConfigHelper $configHelper
     * @param MessageHelper $messageHelper
     * @param DataObject $dataObject
     */
    public function __construct(
        ConfigHelper $configHelper,
        MessageHelper $messageHelper,
        DataObject $dataObject
    )
    {
        $this->configHelper = $configHelper;
        $this->messageHelper = $messageHelper;
        $this->dataObject = $dataObject;
    }

    public function beforeAddRecord(
        MonologLogger $subject,
        int $level,
        string $message,
        array $context = []
    ): array
    {

        if (!empty($context) && isset($context['source']) && $context['source'] === 'slack_notify') {
            return [$level, $message, $context];
        }

        if ($this->configHelper->isSlackNotifierEnabled() && in_array($level, $this->configHelper->getLoggerTypes()))
        {
            $timezone = new \DateTimeZone(date_default_timezone_get() ?: 'UTC');
            $ts = new \DateTime('now', $timezone);
            $ts->setTimezone($timezone);

            $messageInfo = $this->dataObject->setData(
                [
                    'level' => $subject::getLevelName($level),
                    'message' => $message,
                    'date' => $ts->format('d/M/Y h:i:s A'),
                    'context' => $context
                ]
            );
            $block = $this->messageHelper->buildMessage($messageInfo);

            $this->messageHelper->sendMessage($subject::getLevelName($level), $block);


        }
        return [$level, $message, $context];
    }

}