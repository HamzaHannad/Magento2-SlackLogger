<?php

declare(strict_types=1);

namespace Magify\SlackNotifier\Plugin;

use GuzzleHttp\Exception\GuzzleException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magify\SlackNotifier\Helper\Message as MessageHelper;
use Monolog\Logger as MonologLogger;
use Magify\SlackNotifier\Model\ExceptionConsumer;
use Magify\SlackNotifier\Helper\Config as ConfigHelper;
use Magify\SlackNotifier\Model\ExceptionMessage;

class Logger
{
    private $configHelper;
    private $messageHelper;
    private $publisher;
    private $exceptionMessage;

    /**
     * @param ConfigHelper $configHelper
     * @param MessageHelper $messageHelper
     * @param PublisherInterface $publisher
     * @param ExceptionMessage $exceptionMessage
     */
    public function __construct(
        ConfigHelper $configHelper,
        MessageHelper $messageHelper,
        PublisherInterface $publisher,
        ExceptionMessage $exceptionMessage,
    ) {
        $this->configHelper = $configHelper;
        $this->messageHelper = $messageHelper;
        $this->publisher = $publisher;
        $this->exceptionMessage = $exceptionMessage;
    }

    /**
     * @param MonologLogger $subject
     * @param int $level
     * @param string $message
     * @param array $context
     * @return array
     * @throws GuzzleException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function beforeAddRecord(
        MonologLogger $subject,
        int $level,
        string $message,
        array $context = []
    ): array {

        if (!empty($context) && isset($context['source']) && $context['source'] === 'slack_notify') {
            return [$level, $message, $context];
        }

        if ($this->configHelper->isSlackNotifierEnabled() && in_array($level, $this->configHelper->getLoggerTypes())) {
            $isAsync = $this->configHelper->isSendAsync();
            $timezone = new \DateTimeZone(date_default_timezone_get() ?: 'UTC');
            $ts = new \DateTime('now', $timezone);
            $ts->setTimezone($timezone);

            $blocks = $this->exceptionMessage->buildBlockMessage(
                $subject::getLevelName($level),
                $message,
                $ts->format('d/M/Y h:i:s A'),
                $context
            );
            if ($isAsync) {
                $data = [
                    'title' => $subject::getLevelName($level),
                    'blocks' => $blocks,
                    'isAsync' => true
                ];

                $this->publisher->publish(
                    ExceptionConsumer::MAGIFY_SLACK_NOTIFIER_EXCEPTION_QUEUE,
                    json_encode($data)
                );
            } else {
                $this->messageHelper->sendMessage(
                    $subject::getLevelName($level),
                    $blocks
                );
            }
        }
        return [$level, $message, $context];
    }
}
