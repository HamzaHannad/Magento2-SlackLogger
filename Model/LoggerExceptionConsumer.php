<?php declare(strict_types=1);

namespace Magify\SlackNotifier\Model;

use Magify\SlackNotifier\Helper\Message as MessageHelper;

class LoggerExceptionConsumer
{
    public const MAGIFY_SLACKNOTIFIER_SLACK_LOGGER = 'magify.slacknotifier.slack.logger';
    private $messageHelper;

    /**
     * @param MessageHelper $messageHelper
     */
    public function __construct(
        MessageHelper $messageHelper,
    )
    {
        $this->messageHelper = $messageHelper;
    }

    /**
     * Queue consumer process handler
     *
     * @param $message
     * @return void
     */
    public function process($message): void
    {
        $data = json_decode($message, true);

        $isException = $data['isException'];

        if ($isException) {

            $level  = $data['level'];
            $block  = $data['block'];

            $this->messageHelper->notifyException($level, $block);
        } else {

            $title  = $data['title'];
            $message  = $data['message'];
            $channel  = $data['channel'];
            $token  = $data['token'];

            $this->messageHelper->sendCustomMessage($title, $message, true, $channel, $token);
        }
    }

}
