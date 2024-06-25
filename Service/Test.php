<?php declare(strict_types=1);

namespace Magify\SlackNotifier\Service;


use Magify\SlackNotifier\Helper\Message as MessageHelper;
class Test
{
    private $messageHelper;
   public function __construct(
       MessageHelper $messageHelper)
   {
       $this->messageHelper = $messageHelper;
   }

   public function getmessage($l, $b) {
       $this->messageHelper->sendMessage($l, $b);
   }

    public function getbuildmessage($m) {
        return $this->messageHelper->buildMessage($m);
    }
}