<?php

namespace Lycan\Providers\CoreBundle\Entity;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Log;
/**
 *
 * @ORM\Entity
 */
class Event extends Log
{
	
	// I haven't specified an external reference because... I think I might make this possible to be used on others???
	/**
	 * @ORM\ManyToOne(targetEntity="Lycan\Providers\CoreBundle\Entity\BatchExecutions", inversedBy="events")
	 * @ORM\JoinColumn(name="batch_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
	 *
	 */
	private $batch;
	
	
	/**
	 * @ORM\ManyToOne(targetEntity="Lycan\Providers\CoreBundle\Entity\ProviderAuthBase")
	 * @ORM\JoinColumn(name="provider_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
	 *
	 */
	private $provider;
	
	/**
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Property")
	 * @ORM\JoinColumn(name="property_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
	 *
	 */
	private $property;
	
	/**
	 * @return mixed
	 */
	public function getBatch()
	{
		return $this->batch;
	}
	
	/**
	 * @param mixed $batch
	 */
	public function setBatch($batch)
	{
		$this->batch = $batch;
		
		return $this;
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
		
		return $this;
	}
	
	/**
	 * @return mixed
	 */
	public function getProperty()
	{
		return $this->property;
	}
	
	/**
	 * @param mixed $property
	 */
	public function setProperty($property)
	{
		$this->property = $property;
		
		return $this;
	}
	
	
	
	
}