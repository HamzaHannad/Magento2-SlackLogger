<?php declare(strict_types=1);

namespace Magify\SlackNotifier\Model;
use Magento\Framework\App\State;

//use Magify\SlackNotifier\Helper\Message as MessageHelper;

/**
 * Class Consumer used to process OperationInterface messages.
 */
class Consumer
{
    private $messageHelper;
    protected $state;

   public function __construct(/*MessageHelper $messageHelper*/ State $state,)
   {
       $this->state = $state;
       //$this->messageHelper = $messageHelper;
   }

    /**
     * consumer process start
     * @param string $messagesBody
     * @return string
     */
    public function process($request)
    {
        $this->state->setAreaCode('crontab');

        $data = json_decode($request, true);
       // $this->messageHelper->sendMessage($data['level'], $data['block']);
    }
}