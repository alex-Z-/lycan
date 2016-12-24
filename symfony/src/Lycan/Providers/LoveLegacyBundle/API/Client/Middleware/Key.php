<?php

namespace Lycan\Providers\LoveLegacyBundle\API\Client\Middleware;

/**
 * A key for authenticating and signing requests.
 */
class Key
{
	/**
	 * @var string
	 *   The key ID.
	 */
	protected $id;
	
	/**
	 * @var string
	 *   The key secret.
	 */
	protected $secret;
	
	/**
	 * Initializes the key with a key ID and key secret.
	 *
	 * @param string $id
	 *   The key ID.
	 * @param string $secret
	 *   The Base64-encoded key secret.
	 */
	public function __construct($id, $secret)
	{
		$this->id = $id;
		$this->secret = $secret;
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function getSecret()
	{
		return $this->secret;
	}
}
