<?php declare(strict_types=1);

namespace Magify\SlackNotifier\Helper;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\ClientException;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\State;
use Psr\Log\LoggerInterfaceFactory;

class Message
{
    private $loggerFactory;
    private $config;
    private $clientFactory;
    private $customerSession;
    private $state;
    private $requestHttp;

    /**
     * @param LoggerInterfaceFactory $loggerFactory
     * @param Config $config
     * @param ClientFactory $clientFactory
     * @param CustomerSession $customerSession
     * @param State $state
     * @param Http $requestHttp
     */
    public function __construct(
//        Config $config,
        ClientFactory $clientFactory,
        CustomerSession $customerSession,
        State $state,
        Http $requestHttp
    )
    {
//        $this->config = $config;
        $this->clientFactory = $clientFactory;
        $this->customerSession = $customerSession;
        $this->state = $state;
        $this->requestHttp = $requestHttp;
    }

    /**
     * @param $level
     * @param $block
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function sendMessage($level, $block): void
    {
        $channelId = $this->config->getChannelId();
        $token = $this->config->getToken();
        $uri = $this->config->getApiUri();
//        $logger = $this->loggerFactory->create();

        if ($uri && $channelId && $token) {

            /** @var Client $client */
            $client = $this->clientFactory->create([
                'timeout' => $this->config->isSendAsync() ? 0 : $this->config->getTimeout()
            ]);

            try {

                $response = $client->post(
                    $uri,
                    ["headers" =>
                        [
                            "Authorization" => "Bearer " . $token
                        ],
                        "json" => [
                            "text" => $level,
                            "channel" => $channelId,
                            "blocks" => $block
                        ]
                    ]
                );

                $responseContent = json_decode($response->getBody()->getContents(), true);

//                if ($response->getStatusCode() !== 200 || (is_array($responseContent) && isset($responseContent['error']))) {
//                    $logger->critical(
//                        $responseContent['error'],
//                        ['details' => json_encode($responseContent),
//                        'source' => 'slack_notify']
//                    );
//                }

            } catch (Exception|ClientException $e) {
//                $logger->critical($e->getMessage(), ['source' => 'slack_notify']);
            }
        } else {
//            $logger->critical(
//                'One of the Slack credentials is incorrect. (Url or Channel ID or Token)',
//                ['source' => 'slack_notify']
//            );
        }

    }

    /**
     * @param $messageInfo
     * @return false|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function buildMessage($messageInfo): string
    {
        $isLogin = $this->customerSession->isLoggedIn();
        $userName = $isLogin ? $this->customerSession->getCustomer()->getName() : '-';
        $userEmail = $isLogin ? $this->customerSession->getCustomer()->getEmail() : '-';
        $groupId = $isLogin ? $this->customerSession->getCustomer()->getGroupId() : '-';
        $clientIP = $this->requestHttp->getClientIp();
        $level= $messageInfo['level'];
        $date = $messageInfo['date'];
        $message = $messageInfo['message'];
        $context = $messageInfo['context'];
        $area = $this->state->getAreaCode();

        $block = [
            [
                "type" => "divider"
            ],
            [
                "type" => "header",
                "text" => [
                    "type" => "plain_text",
                    "text" => $level
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
     * @param $context
     * @return string
     */
    private function buildBlockContext($context): array
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
