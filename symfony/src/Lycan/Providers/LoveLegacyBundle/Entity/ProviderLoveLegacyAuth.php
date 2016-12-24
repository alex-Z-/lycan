<?php

namespace Lycan\Providers\LoveLegacyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lycan\Providers\CoreBundle\Entity\ProviderAuthBase as ProviderAuthBase;
/**
 * @ORM\Entity
 * @Lycan\Providers\CoreBundle\Annotations\DiscriminatorEntry( value = "providerlovelegacyauth" )
 */
class ProviderLoveLegacyAuth extends ProviderAuthBase
{
	
	// This doesn't need to be an entity field..
	private $providerName = "LoveLegacy";
	
	/**
	 * @return string
	 */
	public function getProviderName()
	{
		return $this->providerName;
	}
	
	/**
	 * @param string $providerName
	 */
	public function setProviderName($providerName)
	{
		$this->providerName = $providerName;
	}
	
	
	
	/**
	 * @ORM\Column(type="string")
	 */
	private $baseUrl;
	
	
	/**
	 * @ORM\Column(type="string")
	 */
	private $client;

	/**
	 * @ORM\Column(type="string")
	 */
	private $secret;
	

	/**
	 * @return mixed
	 */
	public function getBaseUrl()
	{
		return $this->baseUrl;
	}

	/**
	 * @param mixed $baseUrl
	 */
	public function setBaseUrl($baseUrl)
	{
		$this->baseUrl = $baseUrl;
	}

	/**
	 * @return mixed
	 */
	public function getSecret()
	{
		return $this->secret;
	}

	/**
	 * @param mixed $secret
	 */
	public function setSecret($secret)
	{
		$this->secret = $secret;
	}

	
	
	public function getLogValues(){
		return get_object_vars ($this);
	}
	
	/**
	 * @return mixed
	 */
	public function getClient()
	{
		return $this->client;
	}
	
	/**
	 * @param mixed $client
	 */
	public function setClient($client)
	{
		$this->client = $client;
	}
	
	
	
	
}
