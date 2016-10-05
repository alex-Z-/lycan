<?php

namespace Lycan\Providers\TabsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lycan\Providers\CoreBundle\Entity\ProviderAuthBase as ProviderAuthBase;
/**
 * @ORM\Entity
 * @Lycan\Providers\CoreBundle\Annotations\DiscriminatorEntry( value = "providertabsauth" )
 */
class ProviderTabsAuth extends ProviderAuthBase
{

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
