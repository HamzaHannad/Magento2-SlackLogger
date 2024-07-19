<?php

declare(strict_types=1);

namespace Magify\SlackNotifier\Model;

use GuzzleHttp\Exception\GuzzleException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magify\SlackNotifier\Helper\Message;

class CustomMessage
{
    /**
     * @var PublisherInterface
     */
    private $publisher;
    /**
     * @var Message
     */
    private $message;

    /**
     * @param PublisherInterface $publisher
     * @param Message $message
     */
    public function __construct(
        PublisherInterface $publisher,
        Message $message,
    ) {
        $this->publisher = $publisher;
        $this->message = $message;
    }

    /**
     * Send message to slack channel
     *
     * @param $title
     * @param $message
     * @param $isAsync
     * @param $channel
     * @param $token
     * @return void
     * @throws GuzzleException
     * @throws NoSuchEntityException
     */
    public function notifyMessage($title, $message, $isAsync, $channel = null, $token = null)
    {
        $blocks = $this->buildBlockMessage($title, $message);
        if ($isAsync) {
            $data = [
                'title' => $title,
                'blocks' => $blocks,
                'isAsync' => true,
                'channel' => $channel,
                'token' => $token
            ];

            $this->publisher->publish(
                CustomMessageConsumer::MAGIFY_SLACK_NOTIFIER_CUSTOM_MESSAGE_QUEUE,
                json_encode($data)
            );
        } else {
            $this->message->sendMessage(
                $title,
                $blocks,
                $isAsync,
                $channel,
                $token
            );
        }
    }

    /**
     * build block for a message
     *
     * @param string $title
     * @param string $message
     * @return string
     */
    private function buildBlockMessage(string $title, string $message): string
    {
        $block = [
            [
                "type" => "divider"
            ],
            [
                "type" => "context",
                "elements" => [
                    [
                        "type" => "mrkdwn",
                        "text" => "*Title*"
                    ],
                    [
                        "type" => "plain_text",
                        "text" => $title
                    ]
                ]
            ],
            [
                "type" => "context",
                "elements" => [
                    [
                        "type" => "mrkdwn",
                        "text" => "*Message*"
                    ],
                    [
                        "type" => "plain_text",
                        "text" => $message
                    ]
                ]
            ]

        ];

        return json_encode($block);
    }
}
