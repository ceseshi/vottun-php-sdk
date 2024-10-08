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

class ERC721Client extends BaseClient
{
	/**
	* @notice Deploy a new ERC721 contract to the blockchain.
	* @dev Calls the Vottun API to deploy a new ERC721 contract with the specified initial parameters. The deployment operation requires the caller to be authenticated with valid API credentials.
	* @param string $name The name of the ERC721 collection.
	* @param string $symbol The symbol of the ERC721 collection.
	* @param string $alias The alias of the ERC721 collection (optional)
	* @param int $gasLimit The gas limit for the transaction (optional)
	* @return string The transaction hash of the deployment operation.
	* @example deploy('MyToken', 'MTK', 'MyToken', 1000000, 80002)
	*/
	public function deploy(string $name, string $symbol, string $alias = null, int $gasLimit = null): string
	{
		$uri = 'erc/v1/erc721/deploy';

		if (!$this->network) {
			throw new \Exception("Network ID is required to deploy the ERC721 contract");
		}

		if (!$name || !$symbol) {
			throw new \Exception("Name and symbol are required to deploy the ERC721 contract");
		}

		# Prepare the data to be sent to the API
		$data = json_encode(array_filter([
			"network" => $this->network,
			"name" => $name,
			"symbol" => $symbol,
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
	* @notice Mint new ERC721 NFTs and assign them to a recipient address.
	* @dev This function calls the Vottun API to mint new ERC721 NFTs and assign them to the specified recipient address. The caller must be authenticated with valid API credentials and have the necessary permissions to mint new tokens. The caller's account must have a sufficient balance of tokens to cover the minting operation.
	* @param string $recipientAddress The address of the recipient to receive the tokens.
	* @param int $tokenId The ID of the token to mint.
	* @param string $ipfsUri The URI of the IPFS metadata for the token.
	* @param string $ipfsHash The IPFS hash of the metadata for the token.
	* @param int $royaltyPercentage The percentage of royalties to be paid to the creator of the token (optional).
	* @param int $gasLimit The gas limit for the transaction (optional).
	* @return string The transaction hash of the deployment operation.
	*/
	public function mint(string $recipientAddress, int $tokenId, string $ipfsUri, string $ipfsHash, int $royaltyPercentage = null, int $gasLimit = null): string
	{
		$uri = 'erc/v1/erc721/mint';

		if (!$this->validateContract()) {
			throw new \Exception("Contract address and network are required");
		}

		if (!$recipientAddress || !$tokenId || !$ipfsUri || !$ipfsHash) {
			throw new \Exception("Recipient address, token ID, IPFS URI, and IPFS hash are required");
		}

		# Prepare the data to be sent to the API
		$data = json_encode(array_filter([
			"contractAddress" => $this->contractAddress,
			"network" => $this->network,
			"recipientAddress" => $recipientAddress,
			"tokenId" => $tokenId,
			"ipfsUri" => $ipfsUri,
			"ipfsHash" => $ipfsHash,
			"royaltyPercentage" => $royaltyPercentage,
			"gasLimit" => $gasLimit
		]));

		if ($data === false) {
			throw new \Exception("Error encoding data");
		}

		# Send the request to the API
		$response = $this->client->post($uri, $data);

		return $response['txHash'];
	}

	/**
	* @notice Transfers an ERC721 NFT from one address to another.
	* @dev This function calls the Vottun API to execute a `transfer` operation on behalf of the caller. This operation allows the caller to transfer an NFT from the specified sender to the specified recipient. The operation requires the caller to be authenticated with valid API credentials.
	* @param int $id The ID of the token to transfer.
	* @param string $from The address of the sender that will transfer the token.
	* @param string $to The address of the recipient that will receive the token.
	* @return string The transaction hash of the deployment operation.
	*/
	public function transfer(int $id, string $from, string $to): string
	{
		$uri = 'erc/v1/erc721/transfer';

		if (!$this->validateContract()) {
			throw new \Exception("Contract address and network are required");
		}

		if (!$id || !$from || !$to) {
			throw new \Exception("Token ID, sender, and recipient are required");
		}

		# Prepare the data to be sent to the API
		$data = json_encode([
			"contractAddress" => $this->contractAddress,
			"network" => $this->network,
			"id" => $id,
			"from" => $from,
			"to" => $to
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
	* @return int The NFTs balance of the address.
	*/
	public function balanceOf(string $address): int
	{
		$uri = 'erc/v1/erc721/balanceOf';

		if (!$this->validateContract()) {
			throw new \Exception("Contract address and network are required");
		}

		if (!$address) {
			throw new \Exception("Address is required");
		}

		# Prepare the data to be sent to the API
		$data = json_encode([
			"contractAddress" => $this->contractAddress,
			"network" => $this->network,
			"address" => $address
		]);

		if ($data === false) {
			throw new \Exception("Error encoding data");
		}

		# Send the request to the API
		$response = $this->client->post($uri, $data);

		return $response['balance'];
	}

	/**
	* @notice Retrieve the Token URI of the ERC721 collection.
	* @dev This function calls the Vottun API to retrieve the Token URI of the ERC721 collection. The caller must be authenticated with valid API credentials and have the necessary permissions to access the token URI.
	* @return string The Token URI of the ERC721 collection.
	*/
	public function tokenUri(): string
	{
		$uri = 'erc/v1/erc721/tokenUri';

		if (!$this->validateContract()) {
			throw new \Exception("Contract address and network are required");
		}

		# Prepare the data to be sent to the API
		$data = json_encode([
			"contractAddress" => $this->contractAddress,
			"network" => $this->network
		]);

		if ($data === false) {
			throw new \Exception("Error encoding data");
		}

		# Send the request to the API
		$response = $this->client->post($uri, $data);

		return $response['uri'];
	}

	/**
	* @notice Retrieve the owner of a specific ERC721 NFT.
	* @dev This function calls the Vottun API to retrieve the owner of a specific ERC721 NFT. The caller must be authenticated with valid API credentials and have the necessary permissions to access the token owner.
	* @param int $id The ID of the token for which to retrieve the owner.
	* @return string The address of the owner of the token.
	*/
	public function ownerOf(int $id): string
	{
		$uri = 'erc/v1/erc721/ownerOf';

		if (!$this->validateContract()) {
			throw new \Exception("Contract address and network are required");
		}

		if (!$id) {
			throw new \Exception("Token ID is required");
		}

		# Prepare the data to be sent to the API
		$data = json_encode([
			"contractAddress" => $this->contractAddress,
			"network" => $this->network,
			"id" => $id
		]);

		if ($data === false) {
			throw new \Exception("Error encoding data");
		}

		# Send the request to the API
		$response = $this->client->post($uri, $data);

		return $response['owner'];
	}
}
