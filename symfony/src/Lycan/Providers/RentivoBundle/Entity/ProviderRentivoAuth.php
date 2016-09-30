<?php

namespace Lycan\Providers\RentivoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lycan\Providers\CoreBundle\Entity\ProviderAuthBase as ProviderAuthBase;
/** @ORM\Entity*/
class ProviderRentivoAuth extends ProviderAuthBase
{
	
	
	/**
	 * @ORM\Column(type="string")
	 */
	private $client;
	
	/**
	 * @ORM\Column(type="string")
	 */
	private $secret;
	
	/**
	 * @ORM\Column(type="string")
	 */
	private $username;
	
	/**
	 * @ORM\Column(type="string")
	 */
	private $password;
	
	
	
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
	public function getUsername()
	{
		return $this->username;
	}
	
	/**
	 * @param mixed $username
	 */
	public function setUsername($username)
	{
		$this->username = $username;
	}
	
	/**
	 * @return mixed
	 */
	public function getPassword()
	{
		return $this->password;
	}
	
	/**
	 * @param mixed $password
	 */
	public function setPassword($password)
	{
		$this->password = $password;
	}
	
	
	
}
