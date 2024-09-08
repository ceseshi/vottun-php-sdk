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

use Vottun\VottunClient;

class ERC1155Client
{
	private VottunClient $client;
	private string $contractAddress;
	private int $network;

	/**
	* @notice Creates an instance of the ERC1155Client to interact with the ERC1155 API.
	* @dev This constructor initializes the ERC1155Client with a VottunClient. The VottunClient must be configured with the necessary API credentials and settings for interacting with the Vottun API. The ERC1155Client provides methods to deploy ERC1155 contracts, transfer NFTs, and more, utilizing the Vottun API.
	* @param VottunClient $client The VottunClient instance configured with API credentials and settings.
	* @param int $network The network ID where the ERC1155 contract is deployed.
	* @param string $contractAddress The contract address of the ERC1155 contract (optional).
	*/
	public function __construct(VottunClient $client, int $network, string $contractAddress = null)
	{
		$this->client = $client;
		$this->network = $network;
		$this->contractAddress = $contractAddress;
	}

	/**
	* @notice Deploy a new ERC1155 contract to the blockchain.
	* @dev Calls the Vottun API to deploy a new ERC1155 contract with the specified initial parameters. The deployment operation requires the caller to be authenticated with valid API credentials.
	* @param string $name The name of the ERC1155 collection.
	* @param string $symbol The symbol of the ERC1155 collection.
	* @param string $ipfsUri The base uri of the metadata for all the assets in the smart contract.
	* @param string $royaltyRecipient The address where the royalties will be recived.
	* @param int $royaltyValue The percentage of the transaction value that is going to be charged as royalty.
	* @param string $alias The alias of the ERC1155 collection (optional)
	* @param int $gasLimit The gas limit for the transaction (optional)
	* @return string The transaction hash of the deployment operation.
	* @example deploy('MyToken', 'MTK', 'MyToken', 1000000, 80002)
	*/
	public function deploy(string $name, string $symbol, string $ipfsUri, string $royaltyRecipient, int $royaltyValue, string $alias, int $gasLimit = null): string
	{
		$uri = 'erc/v1/erc1155/deploy';

		if (!$this->network) {
			throw new \Exception("Network ID is required to deploy the ERC1155 contract");
		}

		if (!$name || !$symbol || !$ipfsUri || !$royaltyRecipient || !$alias) {
			throw new \Exception("Name, symbol, IPFS URI, royalty recipient, royalty value and alias are required to deploy the ERC1155 contract");
		}

		# Prepare the data to be sent to the API
		$data = json_encode(array_filter([
			"network" => $this->network,
			"name" => $name,
			"symbol" => $symbol,
			"ipfsUri" => $ipfsUri,
			"royaltyRecipient" => $royaltyRecipient,
			"royaltyValue" => $royaltyValue,
			"alias" => $alias,
			"gasLimit" => $gasLimit
		]));

		if ($data === false) {
			throw new \Exception("Error encoding data");
		}

		# Send the request to the API
		$response = $this->client->post($uri, $data);

		# Set the contract address and network for future operations
		if (isset($response['contractAddress']) && isset($response['txHash'])) {
			$this->contractAddress = $response['contractAddress'];
		}

		return $response['txHash'];
	}

	/**
	* @notice Validates the contract address and network ID.
	* @dev This function checks if the contract address and network ID are set in the ERC1155Client instance.
	* @return bool True if the contract address and network ID are set, false otherwise.
	*/
	private function validateContract(): bool
	{
		return !empty($this->contractAddress) && $this->network;
	}

	/**
	* @notice Mint new ERC1155 NFTs and assign them to a recipient address.
	* @dev This function calls the Vottun API to mint new ERC1155 NFTs and assign them to the specified recipient address. The caller must be authenticated with valid API credentials and have the necessary permissions to mint new tokens. The caller's account must have a sufficient balance of tokens to cover the minting operation.
	* @param string $to The account where the tokens will be sent.
	* @param int $tokenId The id of the token that will be minted.
	* @param int $amount The amount of tokens that will be minted.
	* @return string The transaction hash of the deployment operation.
	*/
	public function mint(string $to, int $tokenId, int $amount): string
	{
		$uri = 'erc/v1/erc1155/mint';

		if (!$this->validateContract()) {
			throw new \Exception("Contract address and network are required");
		}

		if (!$to || !$tokenId || !$amount) {
			throw new \Exception("Recipient address, token ID and amount are required");
		}

		# Prepare the data to be sent to the API
		$data = json_encode([
			"contractAddress" => $this->contractAddress,
			"network" => $this->network,
			"to" => $to,
			"id" => $tokenId,
			"amount" => $amount
		]);

		if ($data === false) {
			throw new \Exception("Error encoding data");
		}

		# Send the request to the API
		$response = $this->client->post($uri, $data);

		return $response['txHash'];
	}

	/**
	* @notice Mints different amounts of copies of multiple NFTs with the provided metadata to the given address.
	* @dev This function calls the Vottun API to mint new ERC1155 NFTs and assign them to the specified recipient address. The caller must be authenticated with valid API credentials and have the necessary permissions to mint new tokens. The caller's account must have a sufficient balance of tokens to cover the minting operation.
	* @param string $to The account where the tokens will be sent.
	* @param array $tokenIds The ids of the tokens that will be minted.
	* @param array $amounts The amounts of tokens that will be minted.
	* @return string The transaction hash of the deployment operation.
	*/
	public function mintBatch(string $to, array $tokenIds, array $amounts): string
	{
		$uri = 'erc/v1/erc1155/mintBatch';

		if (!$this->validateContract()) {
			throw new \Exception("Contract address and network are required");
		}

		if (!$to || !$tokenIds || !$amounts) {
			throw new \Exception("Recipient address, token IDs and amounts are required");
		}

		foreach ($tokenIds as $tokenId) {
			if (!is_int($tokenId)) {
				throw new \Exception("All token IDs must be integers");
			}
		}

		foreach ($amounts as $amount) {
			if (!is_int($amount)) {
				throw new \Exception("All amounts must be integers");
			}
		}

		# Prepare the data to be sent to the API
		$data = json_encode([
			"contractAddress" => $this->contractAddress,
			"network" => $this->network,
			"to" => $to,
			"ids" => $tokenIds,
			"amounts" => $amounts
		]);

		if ($data === false) {
			throw new \Exception("Error encoding data");
		}

		# Send the request to the API
		$response = $this->client->post($uri, $data);

		return $response['txHash'];
	}

	/**
	* @notice Transfers an ERC1155 NFT from one address to another.
	* @dev This function calls the Vottun API to execute a `transfer` operation on behalf of the caller. This operation allows the caller to transfer an NFT from the specified sender to the specified recipient. The operation requires the caller to be authenticated with valid API credentials.
	* @param string $to The address of the recipient that will receive the token.
	* @param int $tokenId The ID of the token to transfer.
	* @param int $amount The amount of tokens that will be transfered.
	* @return string The transaction hash of the deployment operation.
	*/
	public function transfer(string $to, int $tokenId, int $amount): string
	{
		$uri = 'erc/v1/erc1155/transfer';

		if (!$this->validateContract()) {
			throw new \Exception("Contract address and network are required");
		}

		if (!$to || !$tokenId || !$amount) {
			throw new \Exception("Recipient, token ID and amount are required");
		}

		# Prepare the data to be sent to the API
		$data = json_encode([
			"contractAddress" => $this->contractAddress,
			"network" => $this->network,
			"to" => $to,
			"id" => $tokenId,
			"amount" => $amount
		]);

		if ($data === false) {
			throw new \Exception("Error encoding data");
		}

		# Send the request to the API
		$response = $this->client->post($uri, $data);

		return $response['txHash'];
	}

	/**
	* @notice Transfers an ERC1155 NFT from one address to another.
	* @dev This function calls the Vottun API to execute a `transfer` operation on behalf of the caller. This operation allows the caller to transfer an NFT from the specified sender to the specified recipient. The operation requires the caller to be authenticated with valid API credentials.
	* @param string $to The address of the recipient that will receive the token.
	* @param array $tokenIds The ids of the tokens that will be transfered.
	* @param array $amounts The amounts of tokens that will be transfered.
	* @return string The transaction hash of the deployment operation.
	*/
	public function transferBatch(string $to, array $tokenIds, array $amounts): string
	{
		$uri = 'erc/v1/erc1155/transferBatch';

		if (!$this->validateContract()) {
			throw new \Exception("Contract address and network are required");
		}

		if (!$to || !$tokenIds || !$amounts) {
			throw new \Exception("Recipient, token IDs and amounts are required");
		}

		foreach ($tokenIds as $tokenId) {
			if (!is_int($tokenId)) {
				throw new \Exception("All token IDs must be integers");
			}
		}

		foreach ($amounts as $amount) {
			if (!is_int($amount)) {
				throw new \Exception("All amounts must be integers");
			}
		}

		# Prepare the data to be sent to the API
		$data = json_encode([
			"contractAddress" => $this->contractAddress,
			"network" => $this->network,
			"to" => $to,
			"ids" => $tokenIds,
			"amounts" => $amounts
		]);

		if ($data === false) {
			throw new \Exception("Error encoding data");
		}

		# Send the request to the API
		$response = $this->client->post($uri, $data);

		return $response['txHash'];
	}

	/**
	* @notice Retrieve the NFTs balance of a given address.
	* @dev This function calls the Vottun API to retrieve the NFTs balance of a specific address. The caller must be authenticated with valid API credentials and have the necessary permissions to access the token balances.
	* @param string $address The address for which to retrieve the balance.
	* @param int $tokenId The ID of the token for which to retrieve the balance.
	* @return int The NFTs balance of the address.
	*/
	public function balanceOf(string $address, int $tokenId): int
	{
		$uri = 'erc/v1/erc1155/balanceOf';

		if (!$this->validateContract()) {
			throw new \Exception("Contract address and network are required");
		}

		if (!$address || !$tokenId) {
			throw new \Exception("Address and token ID are required");
		}

		# Prepare the data to be sent to the API
		$data = json_encode([
			"contractAddress" => $this->contractAddress,
			"network" => $this->network,
			"address" => $address,
			"id" => $tokenId
		]);

		if ($data === false) {
			throw new \Exception("Error encoding data");
		}

		# Send the request to the API
		$response = $this->client->post($uri, $data);

		return $response['balance'];
	}

	/**
	* @notice Retrieve the Token URI of the ERC1155 collection.
	* @dev This function calls the Vottun API to retrieve the Token URI of the ERC1155 collection. The caller must be authenticated with valid API credentials and have the necessary permissions to access the token URI.
	* @return string The Token URI of the ERC1155 collection.
	*/
	public function tokenUri(int $tokenId): string
	{
		$uri = 'erc/v1/erc1155/tokenUri';

		if (!$this->validateContract()) {
			throw new \Exception("Contract address and network are required");
		}

		if (!$tokenId) {
			throw new \Exception("Token ID is required");
		}

		# Prepare the data to be sent to the API
		$data = json_encode([
			"contractAddress" => $this->contractAddress,
			"network" => $this->network,
			"id" => $tokenId
		]);

		if ($data === false) {
			throw new \Exception("Error encoding data");
		}

		# Send the request to the API
		$response = $this->client->post($uri, $data);

		return $response['uri'];
	}

	/**
	* @notice Retrieve the contract address of the ERC1155 contract.
	* @dev Returns the contract address of the ERC1155 contract that is currently being managed by the ERC1155Client instance.
	* @return string The contract address of the ERC1155 contract.
	*/
	public function getContractAddress(): string
	{
		return $this->contractAddress;
	}

	/**
	* @notice Retrieve the network ID of the ERC1155 contract.
	* @dev Returns the network ID of the blockchain network where the ERC1155 contract is deployed.
	* @return int The network ID of the blockchain network.
	*/
	public function getNetwork(): int
	{
		return $this->network;
	}

	/**
	* @notice Set the contract address of the ERC1155 contract.
	* @dev Sets the contract address of the ERC1155 contract to be managed by the ERC1155Client instance.
	* @param string $contractAddress The contract address of the ERC1155 contract.
	*/
	public function setContractAddress(string $contractAddress): void
	{
		$this->contractAddress = $contractAddress;
	}

	/**
	* @notice Set the network ID of the ERC1155 contract.
	* @dev Sets the network ID of the blockchain network where the ERC1155 contract is deployed.
	* @param int $network The network ID of the blockchain network.
	*/
	public function setNetwork(int $network): void
	{
		$this->network = $network;
	}
}
