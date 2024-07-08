<?php declare(strict_types=1);

namespace Magify\SlackNotifier\Plugin;

use Magento\Framework\DataObject;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magify\SlackNotifier\Helper\Message as MessageHelper;
use Monolog\Logger as MonologLogger;
use Magify\SlackNotifier\Helper\Config as ConfigHelper;

class Logger
{
    private $configHelper;
    private $messageHelper;
    private $dataObject;
    private $publisher;

    /**
     * @param ConfigHelper $configHelper
     * @param MessageHelper $messageHelper
     * @param DataObject $dataObject
     * @param PublisherInterface $publisher
     */
    public function __construct(
        ConfigHelper $configHelper,
        MessageHelper $messageHelper,
        DataObject $dataObject,
        PublisherInterface $publisher,
    )
    {
        $this->configHelper = $configHelper;
        $this->messageHelper = $messageHelper;
        $this->dataObject = $dataObject;
        $this->publisher = $publisher;
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
            $isAsync = $this->configHelper->isSendAsync();
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

            if ($isAsync) {
                $data = [
                    'level' => $subject::getLevelName($level),
                    'block' => $block
                ];

                $this->publisher->publish('magify.slacknotifier.slack.logger', json_encode($data));
            } else {
                $this->messageHelper->sendMessage($subject::getLevelName($level), $block);
            }
        }
        return [$level, $message, $context];
    }

}