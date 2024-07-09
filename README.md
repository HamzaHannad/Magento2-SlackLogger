# Magento 2 Slack Notifier Module

## Overview

The Magento 2 Slack Notifier module sends logger exceptions automatically to a specified Slack channel. This integration helps you stay updated with critical issues in your Magento store by sending real-time notifications directly to your Slack workspace.

## Features

- Sends logger exceptions to a Slack channel
- Configurable log levels (Alert, Debug, Critical, Info, Error, Emergency, Notice, Warning)
- Option to use synchronous or asynchronous sending
- Custom message service to send messages to specified channels with async/sync options (for developers)
## Installation

### Using Composer

1. Navigate to your Magento 2 root directory.
2. Require the module using Composer:
    ```bash
    composer require magify/magento2-module-slacknotifier
    ```
3. Enable the module:
    ```bash
    php bin/magento module:enable Magify_SlackNotifier
    ```
4. Run the setup upgrade command:
    ```bash
    php bin/magento setup:upgrade
    ```

## Configuration

1. In the Magento admin panel, navigate to `Stores > Configuration > Advanced > Developer > Slack Notifier`.
2. Configure the following settings:

    - **Activate**: Enable or disable the Slack notifier module.
    - **Use Async Send**: Select whether to send messages asynchronously or synchronously.
    - **API Timeout**: Set the timeout duration in seconds for API calls. Use 0 for an indefinite wait.
    - **Logger Type**: Select the log types to send to Slack (e.g., Alert, Debug, Critical, etc.).
    - **URL**: The Slack API URL. Typically, this will be `https://slack.com/api/chat.postMessage`.
    - **Channel ID**: The ID of the Slack channel where messages will be sent.
    - **Token**: Your Slack app token.

## Usage

Once configured, the module will automatically send log exceptions of the specified types to your Slack channel. You can monitor these notifications to quickly respond to issues in your Magento store.

## Custom Message Service

### Overview

The custom message service allows developers to send any message to a specified Slack channel, with the option to choose between asynchronous or synchronous sending.

### Note

If the channel and token are not set in the function parameters, the service will use the values configured in the Magento admin panel.

### Usage

Here is an example of how to use the custom message service in your Magento 2 module:

1. Inject the `SlackNotifierService` in your class:

    ```php
    <?php

    namespace YourVendorName\SlackNotifier\Controller\Index;

    use Magento\Framework\App\Action\Action;
    use Magento\Framework\App\Action\Context;
    use YourVendorName\SlackNotifier\Service\SlackNotifierService;

    class Test extends Action
    {
        protected $slackNotifierService;

        public function __construct(Context $context, SlackNotifierService $slackNotifierService)
        {
            $this->slackNotifierService = $slackNotifierService;
            parent::__construct($context);
        }

        public function execute()
        {
            $title= "This is a test title";
            $message = "This is a test message";
            $channel = "your-channel-id";
            $token = "your-token";
            $async = false; // or true based on your requirement
            
            $this->slackNotifierService->sendCustomMessage($title, $message, $async, $channel, $token);
        }
    }
    ```

2. Call the `sendCustomMessage` method with your title, message, channel ID, token and sending type (async/sync).


## Support

For support and feature requests, please open an issue on the [GitHub repository](https://github.com/HamzaHannad/Magento2-SlackLogger/issues).

## License

This module is licensed under the MIT License.
