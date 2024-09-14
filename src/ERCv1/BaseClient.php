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

class BaseClient
{
	protected VottunClient $client;
	protected string $contractAddress;
	protected int $network;

	/**
	* @notice Creates an instance of the API Client to interact with the Vottun API.
	* @dev This constructor initializes the Client with a VottunClient. The VottunClient must be configured with the necessary API credentials and settings for interacting with the Vottun API. The Client provides methods to deploy tokens, transfer tokens, and more, utilizing the Vottun API.
	* @param VottunClient $client The VottunClient instance configured with API credentials and settings.
	* @param int $network The network ID where the contract is deployed.
	* @param string $contractAddress The contract address (optional).
	*/
	public function __construct(VottunClient $client, int $network, string $contractAddress = null)
	{
		if (!$client || !$network) {
			throw new \Exception("VottunClient and network are required to interact with the Vottun API");
		}

		$this->client = $client;
		$this->network = $network;
		$this->contractAddress = $contractAddress;
	}

	/**
	* @notice Validates the contract address and network ID.
	* @dev This function checks if the contract address and network ID are set in the API Client instance.
	* @return bool True if the contract address and network ID are set, false otherwise.
	*/
	protected function validateContract(): bool
	{
		return !empty($this->contractAddress) && $this->network;
	}

	/**
	* @notice Retrieve the contract address.
	* @dev Returns the contract address that is currently being managed by the API Client instance.
	* @return string The contract address.
	*/
	public function getContractAddress(): string
	{
		return $this->contractAddress;
	}

	/**
	* @notice Retrieve the network.
	* @dev Returns the network ID of the blockchain where the contract is deployed.
	* @return int The network ID of the blockchain network.
	*/
	public function getNetwork(): int
	{
		return $this->network;
	}

	/**
	* @notice Set the contract address.
	* @dev Sets the address of the contract to be managed by the API Client instance.
	* @param string $contractAddress The contract address.
	*/
	public function setContractAddress(string $contractAddress): void
	{
		if (!$contractAddress) {
			throw new \Exception("Contract address is required");
		}

		$this->contractAddress = $contractAddress;
	}

	/**
	* @notice Set the network ID of the contract.
	* @dev Sets the network ID of the blockchain network where the contract is deployed.
	* @param int $network The network ID.
	*/
	public function setNetwork(int $network): void
	{
		if (!$network) {
			throw new \Exception("Network is required");
		}

		$this->network = $network;
	}
}
