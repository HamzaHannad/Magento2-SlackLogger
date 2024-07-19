<?php

declare(strict_types=1);

namespace Magify\SlackNotifier\Helper;

use Exception;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

class Message
{
    private $logger;
    private $config;
    private $clientFactory;

    /**
     * @param LoggerInterface $logger
     * @param Config $config
     * @param ClientFactory $clientFactory
     */
    public function __construct(
        LoggerInterface $logger,
        Config $config,
        ClientFactory $clientFactory
    ) {
        $this->logger = $logger;
        $this->config = $config;
        $this->clientFactory = $clientFactory;
    }

    /**
     * Send a custom message
     *
     * @param string $title
     * @param string $blocks
     * @param bool $isAsync
     * @param string|null $channel
     * @param string|null $token
     * @return void
     * @throws GuzzleException
     * @throws NoSuchEntityException
     */
    public function sendMessage(
        string $title,
        string $blocks,
        bool $isAsync = false,
        string $channel = null,
        string $token = null
    ): void {
        $channelId = $channel ?? $this->config->getChannelId();
        $token = $token ?? $this->config->getToken();
        $uri = $this->config->getApiUri();

        if ($uri && $channelId && $token) {
            $client = $this->clientFactory->create([
                'timeout' => $isAsync ? 0 : $this->config->getTimeout()
            ]);

            try {
                $response = $client->post(
                    $uri,
                    ["headers" =>
                        [
                            "Authorization" => "Bearer " . $token
                        ],
                        "json" => [
                            "text" => $title,
                            "channel" => $channelId,
                            "blocks" => $blocks
                        ]
                    ]
                );

                $responseContent = json_decode($response->getBody()->getContents(), true);

                if (
                    $response->getStatusCode() !== 200 || (is_array($responseContent)
                        && isset($responseContent['error']))
                ) {
                    $this->logger->critical($responseContent['error'], ['details' => json_encode($responseContent),
                        'source' => 'slack_notify']);
                }
            } catch (Exception | ClientException $e) {
                $this->logger->critical($e->getMessage(), ['source' => 'slack_notify']);
            }
        } else {
            $this->logger->critical(
                'One of the Slack credentials is incorrect. (Url or Channel ID or Token)',
                ['source' => 'slack_notify']
            );
        }
    }
}
