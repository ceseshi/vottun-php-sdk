<?php

/*
 * This file is part of the Vottun PHP SDK package.
 *
 * (c) César Escribano https://github.com/ceseshi
 *
 * This source file is subject to the LGPL that is bundled
 * with this source code in the file LICENSE.
 */

require __DIR__.'/../vendor/autoload.php';

use Vottun\VottunClient;
use Vottun\ERCv1\ERC20Client;

$vottunApiKey = ''; // Replace with your Vottun API key
$vottunApplicationVkn = ''; // Replace with your Vottun application VKN

$network = 80002; // Amoy testnet
$contractAddress = ''; // Token contract address
$ownerAddress = ''; // Token owner address
$otherAddress = ''; // Destination address

$vottunClient = new VottunClient($vottunApiKey, $vottunApplicationVkn);
$erc20Token = new ERC20Client($vottunClient, $network, $contractAddress);

/*# Transfer some tokens to the destination address
$amount = $vottunClient->etherToWei("100");
$response = $erc20Token->transfer($otherAddress, $amount);
echo "Transfer response:\n";
print_r($response);*/

# Increase the allowance of the destination address
$amount = strval(\Web3\Utils::toWei("100.001", 'ether'));
$response = $erc20Token->increaseAllowance($otherAddress, $amount);
echo "increaseAllowance response: {$response}\n";

# Get the allowance of the destination address
$response = $erc20Token->allowance($ownerAddress, $otherAddress);
echo "Allowance of {$otherAddress}: {$response}\n";

# Get the balance of the destination address
$response = $erc20Token->balanceOf($otherAddress);
echo "Balance of {$otherAddress}: {$response}\n";