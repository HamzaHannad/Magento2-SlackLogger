<?php declare(strict_types=1);

namespace Magify\SlackNotifier\Model;

use Magify\SlackNotifier\Helper\Message as MessageHelper;

/**
 * Class Consumer used to process OperationInterface messages.
 */
class Consumer
{
    private $messageHelper;

    /**
     * @param MessageHelper $messageHelper
     */
   public function __construct(MessageHelper $messageHelper)
   {
       $this->messageHelper = $messageHelper;
   }

    /**
     * consumer process start
     * @param string $messagesBody
     * @return string
     */
    public function process($request)
    {
        $data = json_decode($request, true);
        $this->messageHelper->sendMessage($data['level'], $data['block']);
    }
}