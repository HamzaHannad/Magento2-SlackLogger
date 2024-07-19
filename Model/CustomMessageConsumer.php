<?php

declare(strict_types=1);

namespace Magify\SlackNotifier\Model;

use GuzzleHttp\Exception\GuzzleException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magify\SlackNotifier\Helper\Message as MessageHelper;

class CustomMessageConsumer
{
    public const MAGIFY_SLACK_NOTIFIER_CUSTOM_MESSAGE_QUEUE = 'magify.slack.notifier.custom.message';
    /**
     * @var MessageHelper
     */
    private $messageHelper;

    /**
     * @param MessageHelper $messageHelper
     */
    public function __construct(
        MessageHelper $messageHelper
    ) {
        $this->messageHelper = $messageHelper;
    }

    /**
     * Queue consumer process handler
     *
     * @param $message
     * @return void
     * @throws GuzzleException
     * @throws NoSuchEntityException
     */
    public function process($result): void
    {
        $data = json_decode($result, true);

        $this->messageHelper->sendMessage(
            $data['title'],
            $data['blocks'],
            $data['isAsync'],
            $data['channel'],
            $data['token']
        );
    }
}
