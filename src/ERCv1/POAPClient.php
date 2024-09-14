<?php
/*
 * This file is part of the Vottun PHP SDK package.
 *
 * (c) César Escribano https://github.com/ceseshi
 *
 * This source file is subject to the LGPL that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vottun\ERCv1;

class POAPClient extends BaseClient
{
	private VottunClient $client;
	private string $contractAddress;
	private int $network;

	/**
	 * @notice Deploy a new POAP contract to the blockchain.
	 * @param string $name The name of the POAP collection.
	 * @param int $amount The amount of tokens to be minted.
	 * @param string $ipfsUri The base URI of the metadata for all the assets.
	 * @param string $alias The alias of the POAP collection.
	 * @param int|null $gasLimit The gas limit for the transaction (optional).
	 * @return string The transaction hash of the deployment operation.
	 */
	public function deploy(string $name, int $amount, string $ipfsUri, string $alias, int $gasLimit = null): string
	{
		$uri = 'erc/v1/poap/deploy';

		if (!$this->network) {
			throw new \Exception("Network ID is required to deploy the POAP contract");
		}

		if (!$name || !$amount || !$ipfsUri || !$alias) {
			throw new \Exception("Name, amount, IPFS URI, alias and network are required to deploy the POAP contract");
		}

		$data = json_encode(array_filter([
			"name" => $name,
			"amount" => $amount,
			"ipfsUri" => $ipfsUri,
			"network" => $this->network,
			"alias" => $alias,
			"gasLimit" => $gasLimit
		]));

		if ($data === false) {
			throw new \Exception("Error encoding data");
		}

		$response = $this->client->post($uri, $data);

		if (isset($response['contractAddress']) && isset($response['txHash'])) {
			$this->contractAddress = $response['contractAddress'];
		}

		return $response['txHash'];
	}

	/**
	 * @notice Transfer a POAP token to a given address.
	 * @param string $to The recipient's address.
	 * @param int $tokenId The ID of the token to transfer.
	 * @return string The transaction hash of the transfer operation.
	 */
	public function transfer(string $to, int $tokenId): string
	{
		$uri = 'erc/v1/poap/transfer';

		if (!$this->validateContract()) {
			throw new \Exception("Contract address and network are required");
		}

		if (!$to || !$tokenId) {
			throw new \Exception("To and token ID are required");
		}

		$data = json_encode([
			"contractAddress" => $this->contractAddress,
			"network" => $this->network,
			"to" => $to,
			"id" => $tokenId
		]);

		if ($data === false) {
			throw new \Exception("Error encoding data");
		}

		$response = $this->client->post($uri, $data);

		return $response['txHash'];
	}

	/**
	 * @notice Retrieve the POAP balance of a given address.
	 * @param string $address The address for which to retrieve the balance.
	 * @param int $tokenId The ID of the token for which to retrieve the balance.
	 * @return string The POAP balance of the address.
	 */
	public function balanceOf(string $address, int $tokenId): string
	{
		$uri = 'erc/v1/poap/balanceOf';

		if (!$this->validateContract()) {
			throw new \Exception("Contract address and network are required");
		}

		if (!$address || !$tokenId) {
			throw new \Exception("Address and token ID are required");
		}

		$data = json_encode([
			"contractAddress" => $this->contractAddress,
			"network" => $this->network,
			"address" => $address,
			"id" => $tokenId
		]);

		if ($data === false) {
			throw new \Exception("Error encoding data");
		}

		$response = $this->client->post($uri, $data);

		return $response['balance'];
	}

	/**
	 * @notice Retrieve the Token URI of the POAP.
	 * @param int $tokenId The ID of the token for which to retrieve the URI.
	 * @return string The Token URI of the POAP.
	 */
	public function tokenUri(int $tokenId): string
	{
		$uri = 'erc/v1/poap/tokenUri';

		if (!$this->validateContract()) {
			throw new \Exception("Contract address and network are required");
		}

		if (!$tokenId) {
			throw new \Exception("Token ID is required");
		}

		$data = json_encode([
			"contractAddress" => $this->contractAddress,
			"network" => $this->network,
			"id" => $tokenId
		]);

		if ($data === false) {
			throw new \Exception("Error encoding data");
		}

		$response = $this->client->post($uri, $data);

		return $response['uri'];
	}
}
