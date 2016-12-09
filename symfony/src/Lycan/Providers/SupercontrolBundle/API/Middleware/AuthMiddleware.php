<?php

namespace Lycan\Providers\SupercontrolBundle\API\Middleware;

use Guzzle\Http\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;
use Lycan\Providers\SupercontrolBundle\Entity\ProviderSupercontrolAuth;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7;


class AuthMiddleware
{
	
	
	public function __construct(ProviderSupercontrolAuth $auth)
	{
		$this->auth = $auth;
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
		$root = true;
		$document = '';
		
		$body = (String) $request->getBody();
		if($body){
			$xmlstr = $body;
			libxml_use_internal_errors(true);
			$doc = simplexml_load_string($xmlstr);
		}
		
		if ($root) {
			$document .= "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
			$document .= '<scAPI>';
			$document .= '<client>';
			$document .= '<ID>' . $this->auth->getClient() . '</ID>';
			$document .= '<key>' . $this->auth->getSecret() . '</key>';
		 	$document .= '<siteID></siteID>';
		
		}
		
		if($body && $doc){
			$document .= $body;
		}
		
		if ($root) {
			$document .= '</client>';
			$document .= '</scAPI>';
		}
		
		// $body = $request->getBody();
		$request =  $request->withHeader('X-Lycan-Client', 1);
		$request =  $request->withHeader("Content-Type", 'application/xml; charset="utf-8"');
		$stream = Psr7\stream_for($document);
		
		return new Psr7\Request(
			$request->getMethod(),
			$request->getUri(),
			$request->getHeaders(),
			$document
		);
		
		
		// Let's just do it all in here.....
		return $request;
	}
}
