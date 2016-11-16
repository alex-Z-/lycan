<?php

namespace Lycan\Providers\TabsBundle\API\Client\Middleware;

use Lycan\Providers\TabsBundle\API\Client\Middleware\HmacDigest;
use Psr\Http\Message\RequestInterface;

/**
 * Signs requests according to the HTTP HMAC spec.
 */
class RequestSigner
{
	/**
	 * @var \Acquia\Hmac\KeyInterface
	 *   The key to sign requests with.
	 */
	protected $key;
	
	/**
	 * @var string
	 *   The API realm/provider.
	 */
	protected $realm;
	
	/**
	 * @var \Acquia\Hmac\Digest\DigestInterface
	 *   The message digest to use when signing requests.
	 */
	protected $digest;
	
	/**
	 * Initializes the request signer with a key and realm.
	 *
	 * @param \Acquia\Hmac\KeyInterface $key
	 *   The key to sign requests with.
	 * @param string $realm
	 *   The API realm/provider. Defaults to "Acquia".
	 * @param \Acquia\Hmac\Digest\DigestInterface $digest
	 *   The message digest to use when signing requests. Defaults to
	 *   \Acquia\Hmac\Digest\Digest.
	 */
	public function __construct(Key $key, $realm = 'Tabs', HmacDigest $digest = null)
	{
		$this->key = $key;
		$this->realm = $realm;
		$this->digest = $digest ?: new HmacDigest();
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function signRequest(RequestInterface $request, array $customHeaders = [])
	{
		
		$request = $this->getTimestampedRequest($request);
		$request = $this->getContentHashedRequest($request);
		$request = $this->getAuthorizedRequest($request, $customHeaders);
	
		return $request;
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function getTimestampedRequest(RequestInterface $request, \DateTime $date = null)
	{
		if ($request->hasHeader('X-Authorization-Timestamp')) {
			return clone $request;
		}
		
		$date = $date ?: new \DateTime('now', new \DateTimeZone('UTC'));
		
		/** @var RequestInterface $request */
		$request = $request->withHeader('X-Authorization-Timestamp', $date->getTimestamp());
		
		return $request;
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function getContentHashedRequest(RequestInterface $request)
	{
		$body = (string) $request->getBody();
		
		if (!strlen($body)) {
			return clone $request;
		}
		
		$hashedBody = $this->digest->hash((string) $body);
		
		/** @var RequestInterface $request */
		$request =  $request->withHeader('X-Authorization-Content-SHA256', $hashedBody);
		
		return $request;
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function getAuthorizedRequest(RequestInterface $request, array $customHeaders = [])
	{
		$request =  $request->withHeader('X-Lycan-Client', 1);
		
		//dump($request->getUri()->getQuery(), get_class_methods($request->getUri()));die();
		$query = $request->getUri()->getQuery();
		// Mix the query strings...
		parse_str( $query, $params );
		
		$hash = http_build_query( $this->digest->encode($params, $this->key));
	
		$uri = implode("&", array_filter( [ $query, $hash ], 'strlen' ));
		
		$request = $request->withUri(  $request->getUri()->withQuery($uri) );
		return $request;
	}
	

}
