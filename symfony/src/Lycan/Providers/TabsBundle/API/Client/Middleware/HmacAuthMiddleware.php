<?php

namespace Lycan\Providers\TabsBundle\API\Client\Middleware;

use Lycan\Providers\TabsBundle\API\Client\Middleware\Key;

use Lycan\Providers\TabsBundle\API\Client\Middleware\RequestSigner;
use Acquia\Hmac\ResponseAuthenticator;

use Guzzle\Http\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HmacAuthMiddleware
{
	/**
	 * @var \Acquia\Hmac\KeyInterface
	 *  The key with which to sign requests and responses.
	 */
	protected $key;
	
	/**
	 * @var \Acquia\Hmac\RequestSignerInterface
	 */
	protected $requestSigner;
	
	/**
	 * @var array
	 */
	protected $customHeaders = [];
	
	/**
	 * @param \Acquia\Hmac\KeyInterface $key
	 * @param string $realm
	 * @param array $customHeaders
	 */
	public function __construct(Key $key, $realm = 'Tabs', array $customHeaders = [])
	{
		$this->key = $key;
		$this->customHeaders = $customHeaders;
		$this->requestSigner = new RequestSigner($key, $realm);
	}
	
	/**
	 * Called when the middleware is handled.
	 *
	 * @param callable $handler
	 *
	 * @return \Closure
	 */
	public function __invoke(callable $handler)
	{
		return function ($request, array $options) use ($handler) {
			
			$request = $this->signRequest($request);
			
			$promise = function (ResponseInterface $response) use ($request) {
				// Unfortunately, Tabs does not have a response authentication.
				// Keeping in case I need to do something anyway...
				return $response;
			};
			
			return $handler($request, $options)->then($promise);
		};
	}
	
	/**
	 * Signs the request with the appropriate headers.
	 *
	 * @param \Psr\Http\Message\RequestInterface $request
	 *
	 * @return \Psr\Http\Message\RequestInterface
	 */
	public function signRequest(RequestInterface $request)
	{
		
		// Let's just do it all in here.....
		return $this->requestSigner->signRequest($request, $this->customHeaders);
	}
}
