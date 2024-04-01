<?php

/*
 * This file is part of the Vottun PHP SDK package.
 *
 * (c) César Escribano https://github.com/ceseshi
 *
 * This source file is subject to the LGPL that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vottun;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use phpseclib\Math\BigInteger;

class VottunClient {
	protected $client;
	protected $apiKey;
	protected $applicationVkn;

	/**
	 * VottunClient constructor.
	 * @param string $apiKey Vottun API Key
	 * @param string $applicationVkn Vottun application VKN
	 */
	public function __construct(string $apiKey, string $applicationVkn) {
		$this->apiKey = $apiKey;
		$this->applicationVkn = $applicationVkn;
		$this->client = new GuzzleClient([
			'base_uri' => 'https://api.vottun.tech/',
			'timeout'  => 2.0,
			'headers' => [
				'Authorization' => "Bearer {$this->apiKey}",
				'x-application-vkn' => $this->applicationVkn,
				'Accept' => 'application/json',
			],
		]);
	}

	/**
	 * Perform a GET request
	 * @param string $uri URI
	 * @param array $query Query parameters
	 * @return array Response body
	 * @throws \Exception
	 */
	public function get($uri, $query) {
		try {
			$response = $this->client->request('GET', $uri, ['query' => $query]);

			$body = json_decode($response->getBody()->getContents(), true, 512, JSON_BIGINT_AS_STRING);

			# Verify for general HTTP error status codes
			if (isset($body['code'])) {
				throw new \Exception("Vottun API Error: [{$body['code']}] {$body['message']}");
			}

			return $body;
		} catch (RequestException $e) {
			# Capture HTTP errors, such as 404 Not Found
			throw new \Exception("HTTP Request Error: " . $e->getMessage(), 0, $e);
		} catch (GuzzleException $e) {
			# Capture network errors, such as DNS resolution failure
			throw new \Exception("HTTP Client Error: " . $e->getMessage(), 0, $e);
		} catch (\Exception $e) {
			# Capture unexpected errors
			throw new \Exception("Unexpected Error: " . $e->getMessage(), 0, $e);
		}
	}

	/**
	 * Perform a POST request
	 * @param string $uri URI
	 * @param array|string $data Request body
	 * @return array Response body
	 * @throws \Exception
	 */
	public function post($uri, $data) {
		try {
			if (is_array($data)) {
				$response = $this->client->request('POST', $uri, [
					'json' => $data
				]);
			}
			elseif (json_decode($data)) {
				$response = $this->client->request('POST', $uri, [
					'body' => $data,
					'headers' => [
						'Content-Type' => 'application/json',
					]
				]);
			}

			$body = json_decode($response->getBody()->getContents(), true, 512, JSON_BIGINT_AS_STRING);

			# Verify for general HTTP error status codes
			if (isset($body['code'])) {
				throw new \Exception("Vottun API Error: [{$body['code']}] {$body['message']}");
			}

			return $body;
		} catch (RequestException $e) {
			# Capture HTTP errors, such as 404 Not Found
			throw new \Exception("HTTP Request Error: " . $e->getMessage(), 0, $e);
		} catch (GuzzleException $e) {
			# Capture network errors, such as DNS resolution failure
			throw new \Exception("HTTP Client Error: " . $e->getMessage(), 0, $e);
		} catch (\Exception $e) {
			# Capture unexpected errors
			throw new \Exception("Unexpected Error: " . $e->getMessage(), 0, $e);
		}
	}
}