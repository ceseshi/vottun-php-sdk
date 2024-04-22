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

class ERC20Client
{
	private $client;
	private $contractAddress;
	private $network;

	/**
	* @notice Creates an instance of the ERC20Client to interact with the ERC20 API.
	* @dev This constructor initializes the ERC20Client with a VottunClient. The VottunClient must be configured with the necessary API credentials and settings for interacting with the Vottun API. The ERC20Client provides methods to deploy ERC20 tokens, transfer tokens, and more, utilizing the Vottun API.
	* @param VottunClient $client The VottunClient instance configured with API credentials and settings.
	* @param int $network The network ID where the ERC20 token is deployed.
	* @param string $contractAddress The contract address of the ERC20 token (optional).
	*/
	public function __construct(VottunClient $client, int $network, string $contractAddress = null)
	{
		$this->client = $client;
		$this->network = intval($network);
		$this->contractAddress = $contractAddress;
	}

	/**
	* @notice Deploy a new ERC20 token contract to the blockchain.
	* @dev Calls the Vottun API to deploy a new ERC20 token contract with the specified initial parameters. The deployment operation requires the caller to be authenticated with valid API credentials.
	* @param string $name The name of the ERC20 token.
	* @param string $symbol The symbol of the ERC20 token.
	* @param string $alias The alias of the ERC20 token.
	* @param string $initialSupply The initial supply of the ERC20 token, in wei.
	* @param int $gasLimit The gas limit for the transaction (optional)
	* @return string The transaction hash of the deployment operation.
	* @example deploy('MyToken', 'MTK', 'MyToken', 1000000, 80002)
	*/
	public function deploy(string $name, string $symbol, string $alias, string $initialSupply, int $gasLimit = null): string
	{
		$uri = 'erc/v1/erc20/deploy';

		if (!$this->network) {
			throw new \Exception("Network ID is required to deploy ERC20 token.");
		}

		if (!$name || !$symbol || !$initialSupply) {
			throw new \Exception("Name, symbol, and initial supply are required to deploy ERC20 token.");
		}

		$gasLimit = intval($gasLimit);

		# Prepare the data to be sent to the API
		$data = "{
			\"network\": {$this->network},
			\"name\": \"{$name}\",
			\"symbol\": \"{$symbol}\",
			\"alias\": \"{$alias}\",
			\"initialSupply\": {$initialSupply},
			\"gasLimit\": {$gasLimit}
		}";

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
	* @dev This function checks if the contract address and network ID are set in the ERC20Client instance.
	* @return bool True if the contract address and network ID are set, false otherwise.
	*/
	private function validateContract(): bool
	{
		return !empty($this->contractAddress) && intval($this->network);
	}

	/**
	* @notice Transfers ERC20 tokens from the caller's account to another address.
	* @dev This function calls the Vottun API to execute a token transfer operation on behalf of the caller. The caller's account must have a sufficient balance of tokens to cover the transfer amount. This operation requires the caller to be authenticated with valid API credentials.
	* @param string $recipient The address of the recipient to receive the tokens.
	* @param string $amount The amount of tokens to transfer, in wei.
	* @param int $gasLimit The gas limit for the transaction (optional))
	* @return string The transaction hash of the deployment operation.
	*/
	public function transfer(string $recipient, string $amount, int $gasLimit = null): string
	{
		$uri = 'erc/v1/erc20/transfer';

		if (!$this->validateContract()) {
			throw new \Exception("Contract address and network are required.");
		}

		if (!$recipient || !$amount) {
			throw new \Exception("Recipient address and amount are required.");
		}

		$gasLimit = intval($gasLimit);

		# Prepare the data to be sent to the API
		$data = "{
			\"contractAddress\": \"{$this->contractAddress}\",
			\"network\": {$this->network},
			\"recipient\": \"{$recipient}\",
			\"amount\": {$amount},
			\"gasLimit\": {$gasLimit}
		}";

		if (!json_decode($data)) {
			throw new \Exception("Error in data validation.");
		}

		# Send the request to the API
		$response = $this->client->post($uri, $data);

		return $response['txHash'];
	}

	/**
	* @notice Transfers ERC20 tokens from one address to another on behalf of the caller.
	* @dev This function calls the Vottun API to execute a `transferFrom` operation on behalf of the caller. This operation allows the caller to transfer tokens from the specified sender to the specified recipient. The operation requires the caller to be authenticated with valid API credentials.
	* @param string $sender The address of the sender that will transfer the tokens.
	* @param string $recipient The address of the recipient that will receive the tokens.
	* @param string $amount The amount of tokens to transfer, in wei.
	* @param int $gasLimit The gas limit for the transaction (optional).
	* @return string The transaction hash of the deployment operation.
	*/
	public function transferFrom(string $sender, string $recipient, string $amount, int $gasLimit = null): string
	{
		$uri = 'erc/v1/erc20/transferFrom';

		if (!$this->validateContract()) {
			throw new \Exception("Contract address and network are required.");
		}

		if (!$sender || !$recipient || !$amount) {
			throw new \Exception("Sender, recipient, and amount are required to transfer ERC20 token.");
		}

		$gasLimit = intval($gasLimit);

		# Prepare the data to be sent to the API
		$data = "{
			\"contractAddress\": \"{$this->contractAddress}\",
			\"network\": {$this->network},
			\"sender\": \"{$sender}\",
			\"recipient\": \"{$recipient}\",
			\"amount\": {$amount},
			\"gasLimit\": {$gasLimit}
		}";

		# Send the request to the API
		$response = $this->client->post($uri, $data);

		return $response['txHash'];
	}

	/**
	* @notice Approve a spender to spend a specific amount of tokens on behalf of the caller.
	* @dev This function calls the Vottun API to execute an `approve` operation on behalf of the caller. This operation allows the specified spender to spend the specified amount of tokens on behalf of the caller. The operation requires the caller to be authenticated with valid API credentials.
	* @param string $spender The address of the spender to approve.
	* @param string $amount The amount of tokens to approve for spending, in wei.
	* @param int $gasLimit The gas limit for the transaction (optional).
	* @return string The transaction hash of the deployment operation.
	*/
	public function increaseAllowance(string $spender, string $addedValue, int $gasLimit = null): string
	{
		$uri = 'erc/v1/erc20/increaseAllowance';

		if (!$this->validateContract()) {
			throw new \Exception("Contract address and network are required.");
		}

		if (!$spender || !$addedValue) {
			throw new \Exception("Spender and addedValue are required to increase allowance.");
		}

		$gasLimit = intval($gasLimit);

		# Prepare the data to be sent to the API
		$data = "{
			\"contractAddress\": \"{$this->contractAddress}\",
			\"network\": {$this->network},
			\"spender\": \"{$spender}\",
			\"addedValue\": {$addedValue},
			\"gasLimit\": {$gasLimit}
		}";

		$response = $this->client->post($uri, $data);

		return $response['txHash'];
	}

	/**
	* @notice Decrease the allowance of a spender to spend a specific amount of tokens on behalf of the caller.
	* @dev This function calls the Vottun API to execute a `decreaseAllowance` operation on behalf of the caller. This operation decreases the allowance of the specified spender to spend the specified amount of tokens on behalf of the caller. The operation requires the caller to be authenticated with valid API credentials.
	* @param string $spender The address of the spender to decrease the allowance for.
	* @param string $substractedValue The amount of tokens to decrease the allowance by, in wei.
	* @param int $gasLimit The gas limit for the transaction (optional).
	* @return string The transaction hash of the deployment operation.
	*/
	public function decreaseAllowance(string $spender, string $substractedValue, string $gasLimit = null): string
	{
		$uri = 'erc/v1/erc20/decreaseAllowance';

		if (!$this->validateContract()) {
			throw new \Exception("Contract address and network are required.");
		}

		if (!$spender || !$substractedValue) {
			throw new \Exception("Spender and substractedValue are required to decrease allowance.");
		}

		$gasLimit = intval($gasLimit);

		# Prepare the data to be sent to the API
		$data = "{
			\"contractAddress\": \"{$this->contractAddress}\",
			\"network\": {$this->network},
			\"spender\": \"{$spender}\",
			\"substractedValue\": {$substractedValue},
			\"gasLimit\": {$gasLimit}
		}";

		$response = $this->client->post($uri, $data);

		return $response['txHash'];
	}

	/**
	* @notice Retrieve the allowance of a specific ERC-20 token for a given owner and spender.
	* @dev Calls the Vottun API to obtain the allowance of the token associated with the provided contract address for the specified owner and spender. This method performs a read operation and does not require gas.
	* @param string $contractAddress The contract address of the ERC-20 token.
	* @param string $network The network ID where the token is deployed.
	* @param string $owner The address that owns the tokens.
	* @param string $spender The address that is allowed to spend the tokens.
	* @return int The allowance of the ERC-20 token for the specified owner and spender.
	*/
	public function allowance(string $owner, string $spender): string
	{
		$uri = 'erc/v1/erc20/allowance';

		if (!$this->validateContract()) {
			throw new \Exception("Contract address and network are required.");
		}

		# Prepare the data to be sent to the API
		$params = [
			'contractAddress' => $this->contractAddress,
			'network' => $this->network,
			'owner' => $owner,
			'spender' => $spender
		];

		$response = $this->client->get($uri, $params);

		return $response['allowance'];
	}

	/**
	* @notice Retrieve the name of a specific ERC-20 token.
	* @dev Calls the Vottun API to obtain the name of the token associated with the provided contract address. This method performs a read operation and does not require gas.
	* @param string $contractAddress The contract address of the ERC-20 token.
	* @param string $network The network ID where the token is deployed.
	* @return string The name of the ERC-20 token.
	*/
	public function name(): string
	{
		$uri = 'erc/v1/erc20/name';

		if (!$this->validateContract()) {
			throw new \Exception("Contract address and network are required.");
		}

		# Prepare the data to be sent to the API
		$params = [
			'contractAddress' => $this->contractAddress,
			'network' => $this->network
		];

		$response = $this->client->get($uri, $params);

		return $response['name'];
	}

	/**
	* @notice Retrieve the symbol of a specific ERC-20 token.
	* @dev Calls the Vottun API to obtain the symbol of the token associated with the provided contract address. This method performs a read operation and does not require gas.
	* @param string $contractAddress The contract address of the ERC-20 token.
	* @param string $network The network ID where the token is deployed.
	* @return string The symbol of the ERC-20 token.
	*/
	public function symbol(): string
	{
		$uri = 'erc/v1/erc20/symbol';

		if (!$this->validateContract()) {
			throw new \Exception("Contract address and network are required.");
		}

		# Prepare the data to be sent to the API
		$params = [
			'contractAddress' => $this->contractAddress,
			'network' => $this->network
		];

		$response = $this->client->get($uri, $params);

		return $response['symbol'];
	}

	/**
	* @notice Retrieve the total supply of a specific ERC-20 token.
	* @dev Calls the Vottun API to obtain the total supply of the token associated with the provided contract address. This method performs a read operation and does not require gas.
	* @param string $contractAddress The contract address of the ERC-20 token.
	* @param string $network The network ID where the token is deployed.
	* @return int The total supply of the ERC-20 token.
	*/
	public function totalSupply(): string
	{
		$uri = 'erc/v1/erc20/totalSupply';

		if (!$this->validateContract()) {
			throw new \Exception("Contract address and network are required.");
		}

		# Prepare the data to be sent to the API
		$params = [
			'contractAddress' => $this->contractAddress,
			'network' => $this->network
		];

		$response = $this->client->get($uri, $params);

		return $response['totalSupply'];
	}

	/**
	* @notice Retrieve the number of decimals of a specific ERC-20 token.
	* @dev Calls the Vottun API to obtain the number of decimals of the token associated with the provided contract address. This method performs a read operation and does not require gas.
	* @param string $contractAddress The contract address of the ERC-20 token.
	* @param string $network The network ID where the token is deployed.
	* @return int The number of decimals of the ERC-20 token.
	*/
	public function decimals(): string
	{
		$uri = 'erc/v1/erc20/decimals';

		if (!$this->validateContract()) {
			throw new \Exception("Contract address and network are required.");
		}

		# Prepare the data to be sent to the API
		$params = [
			'contractAddress' => $this->contractAddress,
			'network' => $this->network
		];

		$response = $this->client->get($uri, $params);

		return $response['decimals'];
	}

	/**
	* @notice Retrieve the balance of a specific ERC-20 token for a given address.
	* @dev Calls the Vottun API to obtain the balance of the token associated with the provided contract address for the specified address. This method performs a read operation and does not require gas.
	* @param string $contractAddress The contract address of the ERC-20 token.
	* @param string $network The network ID where the token is deployed.
	* @param string $address The address for which to retrieve the token balance.
	* @return int The balance of the ERC-20 token for the specified address.
	*/
	public function balanceOf(string $address): string
	{
		$uri = 'erc/v1/erc20/balanceOf';

		if (!$this->validateContract()) {
			throw new \Exception("Contract address and network are required.");
		}

		# Prepare the data to be sent to the API
		$params = [
			'contractAddress' => $this->contractAddress,
			'network' => $this->network,
			'address' => $address
		];

		$response = $this->client->get($uri, $params);

		return $response['balance'];
	}

	/**
	* @notice Retrieve the contract address of the ERC-20 token.
	* @dev Returns the contract address of the ERC-20 token that is currently being managed by the ERC20Client instance.
	* @return string The contract address of the ERC-20 token.
	*/
	public function getContractAddress(): string
	{
		return $this->contractAddress;
	}

	/**
	* @notice Retrieve the network ID of the ERC-20 token.
	* @dev Returns the network ID of the blockchain network where the ERC-20 token is deployed.
	* @return int The network ID of the blockchain network.
	*/
	public function getNetwork(): int
	{
		return $this->network;
	}

	/**
	* @notice Set the contract address of the ERC-20 token.
	* @dev Sets the contract address of the ERC-20 token to be managed by the ERC20Client instance.
	* @param string $contractAddress The contract address of the ERC-20 token.
	*/
	public function setContractAddress($contractAddress): void
	{
		$this->contractAddress = $contractAddress;
	}

	/**
	* @notice Set the network ID of the ERC-20 token.
	* @dev Sets the network ID of the blockchain network where the ERC-20 token is deployed.
	* @param int $network The network ID of the blockchain network.
	*/
	public function setNetwork($network): void
	{
		$this->network = intval($network);
	}
}
