<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use AppBundle\Entity\STI\ChannelBridge as ChannelBridge;
/**
 * @ORM\Entity
 */
class ChannelBrand extends ChannelBridge
{
	
	/**
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Brand", inversedBy="channels")
	 * @ORM\JoinColumn(name="brand_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
	 *
	 */
	private $brand;

	/**
	 * @ORM\ManyToOne(targetEntity="Lycan\Providers\CoreBundle\Entity\ProviderAuthBase", inversedBy="brandChannels")
	 * @ORM\JoinColumn(name="provider_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
	 *
	 */
	private $provider;


	
	/**
	 * @return mixed
	 */
	public function getBrand()
	{
		return $this->brand;
	}
	
	/**
	 * @param mixed $brand
	 */
	public function setBrand($brand)
	{
		$this->brand = $brand;
	}
	
	/**
	 * @return \Ramsey\Uuid\Uuid
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * @param \Ramsey\Uuid\Uuid $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * @return mixed
	 */
	public function getProvider()
	{
		return $this->provider;
	}

	/**
	 * @param mixed $provider
	 */
	public function setProvider($provider)
	{
		$this->provider = $provider;
	}


	


}
