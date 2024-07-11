<?php

declare(strict_types=1);

namespace Magify\SlackNotifier\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Monolog\Logger;

class LoggerType implements OptionSourceInterface
{
    /**
     * @return array[]
     */
    public function toOptionArray()
    {
        return [
            ['value' => Logger::ALERT, 'label' => 'Alert'],
            ['value' => Logger::DEBUG, 'label' => 'Debug'],
            ['value' => Logger::CRITICAL, 'label' => 'Critical'],
            ['value' => Logger::INFO, 'label' => 'Info'],
            ['value' => Logger::ERROR, 'label' => 'Error'],
            ['value' => Logger::EMERGENCY, 'label' => 'Emergency'],
            ['value' => Logger::NOTICE, 'label' => 'Notice'],
            ['value' => Logger::WARNING, 'label' => 'Warning']
        ];
    }
}
