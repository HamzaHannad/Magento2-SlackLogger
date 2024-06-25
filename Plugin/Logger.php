<?php declare(strict_types=1);

namespace Magify\SlackNotifier\Plugin;

use Magento\Framework\DataObject;
use Monolog\Logger as MonologLogger;
//use Magify\SlackNotifier\Helper\Config as ConfigHelper;
use Magify\SlackNotifier\Service\Test;
//use Magento\Framework\MessageQueue\PublisherInterface;

class Logger
{
    private $configHelper;
    private $publisher;
    private $dataObject;
    private $test;

    /**
     * @param ConfigHelper $configHelper
     * @param PublisherInterface $publisher
     * @param DataObject $dataObject
     */
    public function __construct(
//        ConfigHelper $configHelper,
//        PublisherInterface $publisher,
        DataObject $dataObject,
        Test $test
    )
    {
//        $this->configHelper = $configHelper;
//        $this->publisher = $publisher;
        $this->dataObject = $dataObject;
        $this->test = $test;
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

//        if ($this->configHelper->isSlackNotifierEnabled() && in_array($level, $this->configHelper->getLoggerTypes()))
//        {
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
            $block = $this->test->getbuildmessage($messageInfo);
            if (false) {
//                $data = [
//                    'level' => $subject::getLevelName($level),
//                    'block' => $block
//                ];
//
//                $this->publisher->publish(
//                    'slack.notify.logger',
//                    json_encode($data)
//                );
            } else {
                $this->test->getmessage($subject::getLevelName($level), $block);
            }

//        }
        return [$level, $message, $context];
    }

}