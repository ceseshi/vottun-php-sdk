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
$destAddress = ''; // Destination address

$vottunClient = new VottunClient($vottunApiKey, $vottunApplicationVkn);
$erc20Token = new ERC20Client($vottunClient, $network, null);

# Deploy a new ERC20 token
$initialSupply = \Web3\Utils::toWei("1000000", 'ether');
try {
	$response = $erc20Token->deploy('TestToken', 'TST', 'TestToken', $initialSupply);
	echo "Deploy OK, txHash: {$response}\n";
} catch (\Exception $e) {
	exit("Error deploying ERC20 token: {$e->getMessage()}\n");
}

$address = $erc20Token->getContractAddress();

if (!$address) {
	exit("Error getting contract address\n");
}

# Transfer some tokens to the destination address
$amount = \Web3\Utils::toWei("100", 'ether');
try {
	$response = $erc20Token->transfer($destAddress, $amount);
	echo "Transfer OK, txHash: {$response}\n";
} catch (\Exception $e) {
	exit("Error transferring tokens: {$e->getMessage()}\n");
}

# Get the balance of the destination address
try {
	$response = $erc20Token->balanceOf($destAddress);
	echo "Balance of {$destAddress}: {$response}\n";
} catch (\Exception $e) {
	exit("Error getting balance: {$e->getMessage()}\n");
}
