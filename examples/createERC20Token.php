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
$response = $erc20Token->deploy('TestToken', 'TST', 'TestToken', $initialSupply);
echo "Deploy response: {$response}\n";

if ($erc20Token->getContractAddress()) {
	# Transfer some tokens to the destination address
	$amount = \Web3\Utils::toWei("100", 'ether');
	$response = $erc20Token->transfer($destAddress, $amount);
	echo "Transfer response {$response}:\n";

	# Get the balance of the destination address
	$response = $erc20Token->balanceOf($destAddress);
	echo "Balance of $destAddress: {$response}\n";
}