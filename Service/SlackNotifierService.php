<?php declare(strict_types=1);

namespace Magify\SlackNotifier\Service;

use Magento\Framework\MessageQueue\PublisherInterface;
use Magify\SlackNotifier\Helper\Message;
use Magify\SlackNotifier\Model\LoggerExceptionConsumer;

/**
 * class SlackNotifierService
 */
class SlackNotifierService
{

    private $message;
    private $publisher;

    /**
     * @param Message $message
     * @param PublisherInterface $publisher
     */
    public function __construct(
        Message  $message,
        PublisherInterface $publisher
    )
    {
        $this->message = $message;
        $this->publisher = $publisher;
    }

    /**
     * Send a custom message
     *
     * @param $title
     * @param $message
     * @param $isAsync
     * @param $channel
     * @param $token
     * @return void
     */
    public function sendCustomMessage($title, $message, $isAsync = false, $channel = null, $token = null): void
    {
        if ($isAsync) {
            $data = [
                'isException' => false,
                'title' => $title,
                'message' => $message,
                'channel' => $channel,
                'token' => $token,
            ];

            $this->publisher->publish(LoggerExceptionConsumer::MAGIFY_SLACKNOTIFIER_SLACK_LOGGER, json_encode($data));
        } else {
            $this->message->sendCustomMessage($title, $message, $isAsync, $channel, $token);
        }
    }
}