<?php

declare(strict_types=1);

namespace Magify\SlackNotifier\Model;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magify\SlackNotifier\Helper\Message as MessageHelper;

class ExceptionMessage
{
    /**
     * @var CustomerSession
     */
    private $customerSession;
    /**
     * @var State
     */
    private $state;
    /**
     * @var Http
     */
    private $requestHttp;

    /**
     * @param CustomerSession $customerSession
     * @param State $state
     * @param MessageHelper $message
     */
    public function __construct(
        CustomerSession $customerSession,
        State $state,
        Http $requestHttp
    ) {
        $this->customerSession = $customerSession;
        $this->state = $state;
        $this->requestHttp = $requestHttp;
    }

    /**
     * build block of exception message
     *
     * @param $title
     * @param $message
     * @param $date
     * @param $context
     * @return string
     * @throws LocalizedException
     */
    public function buildBlockMessage($title, $message, $date, $context): string
    {
        $isLogin = $this->customerSession->isLoggedIn();
        $userName = $isLogin ? $this->customerSession->getCustomer()->getName() : '-';
        $userEmail = $isLogin ? $this->customerSession->getCustomer()->getEmail() : '-';
        $groupId = $isLogin ? $this->customerSession->getCustomer()->getGroupId() : '-';
        $clientIP = $this->requestHttp->getClientIp();
        $area = $this->state->getAreaCode();

        $block = [
            [
                "type" => "divider"
            ],
            [
                "type" => "header",
                "text" => [
                    "type" => "plain_text",
                    "text" => $title
                ]
            ],
            [
                "type" => "context",
                "elements" => [
                    [
                        "type" => "mrkdwn",
                        "text" => "*User* : $userName\n*Email* : $userEmail\n*Group Id* : $groupId"
                    ],
                    [
                        "type" => "plain_text",
                        "text" => " "
                    ],
                    [
                        "type" => "mrkdwn",
                        "text" => "*Date* : `$date`\n*Address IP* : $clientIP\n*Area* : $area"
                    ]
                ]
            ],
            [
                "type" => "divider"
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

        if (count($context)) {
            $block[] = $this->buildBlockContext($context);
        }

        return json_encode($block);
    }

    /**
     * build context block of exception message
     *
     * @param array $context
     * @return array
     */
    private function buildBlockContext(array $context): array
    {
        $blockMessage = '';

        foreach ($context as $key => $value) {
            if (is_string($key)) {
                $blockMessage .=  strtoupper($key) . "\n" . $value . "\n\n\n";
            } else {
                $blockMessage .= $value . "\n\n\n";
            }
        }

        return [
            "type" => "rich_text",
            "elements" => [
                [
                    "type" => "rich_text_preformatted",
                    "elements" => [
                        [
                            "type" => "text",
                            "text" => rtrim($blockMessage, "\n")
                        ]
                    ],
                    "border" => 0
                ]
            ]
        ];
    }
}
