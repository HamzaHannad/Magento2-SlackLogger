<?php declare(strict_types=1);

namespace Magify\SlackNotifier\Model;

use Magify\SlackNotifier\Helper\Message as MessageHelper;

class LoggerExceptionConsumer
{
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

        $level  = $data['level'];
        $block  = $data['block'];

        $this->messageHelper->sendMessage($level, $block);
    }

}
