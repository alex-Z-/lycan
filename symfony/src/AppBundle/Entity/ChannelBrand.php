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
	 * @ORM\JoinColumn(name="brand_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
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
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	private $pushInProgress;
	
	/**
	 * @ORM\OneToOne(targetEntity="Lycan\Providers\CoreBundle\Entity\BatchExecutions")
	 * @ORM\JoinColumn(name="last_active_batch", referencedColumnName="id", onDelete="SET NULL")
	 */
	private $lastActiveBatch;
	
	/**
	 * @var \DateTime $contentChanged
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $lastPushCompletedAt;
	
	/**
	 * @var \DateTime $contentChanged
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $lastPushStartedAt;

	
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
	
	/**
	 * @return mixed
	 */
	public function getPushInProgress()
	{
		return $this->pushInProgress;
	}
	
	/**
	 * @param mixed $pushInProgress
	 */
	public function setPushInProgress($pushInProgress)
	{
		$this->pushInProgress = $pushInProgress;
	}
	
	/**
	 * @return mixed
	 */
	public function getLastActiveBatch()
	{
		return $this->lastActiveBatch;
	}
	
	/**
	 * @param mixed $lastActiveBatch
	 */
	public function setLastActiveBatch($lastActiveBatch)
	{
		$this->lastActiveBatch = $lastActiveBatch;
	}
	
	/**
	 * @return \DateTime
	 */
	public function getLastPushCompletedAt()
	{
		return $this->lastPushCompletedAt;
	}
	
	/**
	 * @param \DateTime $lastPushCompletedAt
	 */
	public function setLastPushCompletedAt($lastPushCompletedAt)
	{
		$this->lastPushCompletedAt = $lastPushCompletedAt;
	}
	
	/**
	 * @return \DateTime
	 */
	public function getLastPushStartedAt()
	{
		return $this->lastPushStartedAt;
	}
	
	/**
	 * @param \DateTime $lastPushStartedAt
	 */
	public function setLastPushStartedAt($lastPushStartedAt)
	{
		$this->lastPushStartedAt = $lastPushStartedAt;
	}
	
	


	


}
