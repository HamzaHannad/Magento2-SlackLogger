<?php

declare(strict_types=1);

namespace Magify\SlackNotifier\Model;

use GuzzleHttp\Exception\GuzzleException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magify\SlackNotifier\Helper\Message as MessageHelper;

class ExceptionConsumer
{
    public const MAGIFY_SLACK_NOTIFIER_EXCEPTION_QUEUE = 'magify.slack.notifier.exception';
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
     * @param $result
     * @return void
     * @throws GuzzleException
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function process($result): void
    {
        $data = json_decode($result, true);

        $title  = $data['title'];
        $blocks  = $data['blocks'];
        $isAsync  = $data['isAsync'];

        $this->messageHelper->sendMessage(
            $title,
            $blocks,
            $isAsync
        );
    }
}
