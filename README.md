# Vottun PHP SDK

The [Vottun](https://vottun.com/) PHP SDK provides an easy-to-use PHP interface to interact with the [Vottun API](https://app.vottun.io/), initially designed for operations with ERC20 tokens on the Ethereum blockchain. This SDK simplifies the process of integrating Vottun API functionalities into your PHP applications, including token transfers, querying balances, and managing allowances.

This software is not officially affiliated with Vottun.

[![License: LGPL v3](https://img.shields.io/badge/License-LGPL_v3-blue.svg)](https://www.gnu.org/licenses/lgpl-3.0)

## Author

- [César Escribano](https://github.com/ceseshi)

## Features

- Deploy and transfer ERC20 tokens
- Manage ERC20 token allowances
- Query ERC20 token balances
- Support for big number operations
- Easy to integrate with PHP projects

## Requirements

- PHP >=7.0
- Composer
- A Vottun App ID and API key (https://app.vottun.io/)

## Installation

Run the following command in your project directory to add the Vottun PHP SDK as a dependency:

```bash
composer require ceseshi/vottun-php-sdk
```

# Usage

Here's a quick start guide on how to use the Vottun PHP SDK in your project:

## Initialize the Client

```php
require_once 'vendor/autoload.php';

use Vottun\VottunClient;
use Vottun\ERCv1\ERC20Client;

$vottunApiKey = 'your_api_key_here';
$vottunApplicationVkn = 'your_application_vkn_here';
$network = 80001; // Mumbai
$contractAddress = 'your_contract_address_here';

$vottunClient = new VottunClient($vottunApiKey, $vottunApplicationVkn);
$erc20token = new ERC20Client($vottunClient, $network, $contractAddress);
```

## Transfer Tokens

```php
$recipientAddress = 'recipient_address_here';
$amount = strval(\Web3\Utils::toWei("100.001", 'ether')); // Amount in Wei
$transactionHash = $erc20token->transfer($recipientAddress, $amount);
echo "Transaction hash: {$transactionHash}";
```

## Query Token balance

```php
$address = 'address_to_query_here';
$balance = $erc20token->balanceOf($address);
echo "Token balance: {$balance}";
```

# Pending features

- ERC721 Client
- ERC1155 Client
- POAP Client
- Web3 Core Client
- IPFS Client
- Custodied Wallets Client
- Balances Client
- Estimate Gas Client

# Contributing

Contributions to the Vottun PHP SDK are welcome. Please ensure that your contributions adhere to the following guidelines:

- Fork the repository and create your branch from main.
- If you've added code that should be tested, add tests.
- Ensure the test suite passes.
- Issue that pull request!

# Support

If you encounter any issues or require assistance, please open an issue on the GitHub repository.

# License

This project is licensed under the LGPL. See the LICENSE file for details.

# Acknowledgments

- Thanks to the [Vottun](https://vottun.com/) team for providing the API.
