<?php

declare(strict_types=1);

namespace Magify\SlackNotifier\Service;

use GuzzleHttp\Exception\GuzzleException;
use Magento\Framework\Exception\NoSuchEntityException;
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
        Message $message,
        PublisherInterface $publisher
    ) {
        $this->message = $message;
        $this->publisher = $publisher;
    }

    /**
     * Send a custom message
     *
     * @param string $title
     * @param string $message
     * @param bool $isAsync
     * @param string|null $channel
     * @param string|null $token
     * @return void
     * @throws GuzzleException
     * @throws NoSuchEntityException
     */
    public function sendCustomMessage(
        string $title,
        string $message,
        bool $isAsync = false,
        string $channel = null,
        string $token = null
    ): void {
        if ($isAsync) {
            $data = [
                'isException' => false,
                'title' => $title,
                'message' => $message,
                'channel' => $channel,
                'token' => $token,
            ];

            $this->publisher->publish(
                LoggerExceptionConsumer::MAGIFY_SLACKNOTIFIER_SLACK_LOGGER,
                json_encode($data)
            );
        } else {
            $this->message->sendCustomMessage($title, $message, $isAsync, $channel, $token);
        }
    }
}
