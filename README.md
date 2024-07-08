# Magento 2 Slack Notifier Module

## Overview

The Magento 2 Slack Notifier module sends logger exceptions automatically to a specified Slack channel. This integration helps you stay updated with critical issues in your Magento store by sending real-time notifications directly to your Slack workspace.

## Features

- Sends logger exceptions to a Slack channel
- Configurable log levels (Alert, Debug, Critical, Info, Error, Emergency, Notice, Warning)
- Option to use synchronous or asynchronous sending
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

## Support

For support and feature requests, please open an issue on the [GitHub repository](https://github.com/HamzaHannad/Magento2-SlackLogger/issues).

## License

This module is licensed under the MIT License.
