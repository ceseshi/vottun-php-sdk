# Vottun PHP SDK

The [Vottun](https://vottun.com/) PHP SDK provides an easy-to-use PHP interface for interacting with the [Vottun API](https://app.vottun.io/). Initially implemented for ERC20 and ERC721 token operations on Ethereum compatible blockchains, this SDK simplifies the process of integrating Vottun API functionalities into your PHP applications, including contract deploys, token transfers, querying balances and managing allowances.

[![License: LGPL v3](https://img.shields.io/badge/License-LGPL_v3-blue.svg)](https://www.gnu.org/licenses/lgpl-3.0)

## Author

- [César Escribano](https://github.com/ceseshi)

## Features

- Deploy and mint ERC20 tokens
- Deploy and mint ERC721 NFTs
- Transfer tokens
- Manage allowances
- Query token balances
- Support for big number operations
- Easy to integrate with PHP projects

## Requirements

- PHP >=7.0
- Composer
- A Vottun App ID and API key (https://app.vottun.io/)

## Folder structure

```folder
├── examples                  # Example scripts
├── lib                       # Libraries
│   └── Web3                  # web3p/web3.php library
│       └── Utils.php         # The web3.php Utils class, used to manage big numbers.
└── src                       # Source files
    ├── VottunClient.php      # The main VottunClient class, used to interact with the Vottun API.
    └── ERCv1                 # Vottun ERC v1 API clients
        ├── ERC20Client.php   # The ERC20Client class, used to interact with ERC20 tokens.
        └── ERC721Client.php  # The ERC721Client class, used to interact with ERC721 tokens.
```

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
$vottunClient = new VottunClient($vottunApiKey, $vottunApplicationVkn);
$network = 80002; // Amoy testnet
```

## Deploy ERC20

```php
$erc20token = new ERC20Client($vottunClient, $network, null);

$name = 'MyToken';
$symbol = 'MTK';
$decimals = 18;
$initialSupply = strval(\Web3\Utils::toWei("1000000", 'ether')); // Initial supply in Wei

$transactionHash = $erc20token->deploy($name, $symbol, $decimals, $initialSupply);
$contractAddress = $erc20token->getContractAddress();

echo "Deploy hash: {$transactionHash}";
echo "Deploy address: {$contractAddress}";
```

## Transfer ERC20

```php
$contractAddress = 'your_contract_address_here';
$erc20token = new ERC20Client($vottunClient, $network, $contractAddress);

$recipientAddress = 'recipient_address_here';
$amount = strval(\Web3\Utils::toWei("100.001", 'ether')); // Amount in Wei

$transactionHash = $erc20token->transfer($recipientAddress, $amount);
$balance = $erc20token->balanceOf($recipientAddress);

echo "Transfer hash: {$transactionHash}";
echo "Recipient balance: {$balance}";
```

## Mint ERC721

```php
$contractAddress = 'your_contract_address_here';
$erc721token = new ERC721Client($vottunClient, $network, $contractAddress);

$recipientAddress = 'recipient_address_here';
$ipfsUri = 'ipfs_uri_here';
$ipfsHash = 'ipfs_hash_here';
$royaltyPercentage = 10;
$tokenId = 1;

$transactionHash = $erc721token->mint($recipientAddress, $tokenId, $ipfsUri, $ipfsHash, $royaltyPercentage);
echo "Mint hash: {$transactionHash}";
```

# Pending features

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
