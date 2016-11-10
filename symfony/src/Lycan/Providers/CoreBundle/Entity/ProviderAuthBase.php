<?php

namespace Lycan\Providers\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * @ORM\Entity
 * @ORM\InheritanceType( "SINGLE_TABLE" )
 * @ORM\DiscriminatorColumn( name = "discr", type = "string" )
 * @Lycan\Providers\CoreBundle\Annotations\DiscriminatorEntry( value = "ProviderAuthBase" )
 */
class ProviderAuthBase
{
	const TO_STRING_SEPARATOR = ":-";
	const TO_STRING_PREFIX_SPACER = " ";
	const TO_STRING_SUFFIX_SPACER = " ";
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue("AUTO")
	 */
	private $id;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User")
	 */
	private $owner;
	
	/**
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\ChannelBrand", mappedBy="provider")
	 
	 */
	private $brandChannels;
	
	
	
	/**
	 * @var \DateTime $created
	 *
	 * @Gedmo\Timestampable(on="create")
	 * @ORM\Column(type="datetime")
	 */
	private $createdAt;
	
	
	/**
	 * @var \DateTime $contentChanged
	 *
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $lastPullCompletedAt;
	
	
	/**
	 * @var \DateTime $contentChanged
	 *
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $lastPullStartedAt;
	
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
	 * @var \DateTime $updated
	 *
	 * @Gedmo\Timestampable(on="update")
	 * @ORM\Column(type="datetime")
	 */
	private $updatedAt;
	
	/**
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	private $pullInProgress;
	
	/**
	 * @ORM\OneToOne(targetEntity="Lycan\Providers\CoreBundle\Entity\BatchExecutions")
	 * @ORM\JoinColumn(name="last_active_batch", referencedColumnName="id", onDelete="SET NULL")
	 */
	private $lastActiveBatch;
	
	/**
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	private $pushInProgress;
	
	
	/**
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	private $shouldPull;
	
	/**
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	private $allowPush;
	
	
	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $nickname;
	
	public function getProviderType(){
		return $this->getProviderName();
	}
	
	/**
	 * @return mixed
	 */
	public function getNickname()
	{
		return $this->nickname;
	}
	
	/**
	 * @param mixed $nickname
	 */
	public function setNickname($nickname = null)
	{
		$this->nickname = $nickname;
	}
	
	
	
	
	
	/**
	 * @return mixed
	 */
	public function getOwner()
	{
		return $this->owner;
	}
	
	/**
	 * @param mixed $owner
	 */
	public function setOwner($owner)
	{
		$this->owner = $owner;
	}
	
	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * @param mixed $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}
	
	/**
	 * @return mixed
	 */
	public function getBrandChannels()
	{
		return $this->brandChannels;
	}
	
	/**
	 * @param mixed $brandChannels
	 */
	public function setBrandChannels($brandChannels)
	{
		$this->brandChannels = $brandChannels;
	}
	
	
	public function __toString()
	{
		if(!$this->getId()){
			return "Create a Provider";
		}
		
		$parts = [];
		
		// This shows if it's attached to a channel....
		if($this->getBrandChannels() && !$this->getBrandChannels()->isEmpty() && method_exists($this, "getProviderName")){
			// $prefix = $this->getBrandChannels()->current()->getBrand()->getBrandName();
			$parts[] = sprintf("%s" .$this->getProviderName() . "%s", self::TO_STRING_PREFIX_SPACER, self::TO_STRING_SUFFIX_SPACER);
		} else if( method_exists($this, "getProviderName") ) {
			$parts[] = sprintf("%s Unattached(" .$this->getProviderName() . ")%s", self::TO_STRING_PREFIX_SPACER, self::TO_STRING_SUFFIX_SPACER);
		} else {
			$parts[] = sprintf("%s Unattached(BASE)%s", self::TO_STRING_PREFIX_SPACER, self::TO_STRING_SUFFIX_SPACER);
		}
		
		$parts[] =  sprintf("%s" . $this->getNickName(), self::TO_STRING_PREFIX_SPACER);
		
		if(!$this->getAllowPush()){
			$parts[] = "(not active)";
		}
		
		
		return trim(implode($parts, self::TO_STRING_SEPARATOR));
	}
	
	/**
	 * @return \DateTime
	 */
	public function getCreatedAt()
	{
		return $this->createdAt;
	}
	
	/**
	 * @param \DateTime $createdAt
	 */
	public function setCreatedAt($createdAt)
	{
		$this->createdAt = $createdAt;
	}
	
	/**
	 * @return \DateTime
	 */
	public function getLastPullCompletedAt()
	{
		return $this->lastPullCompletedAt;
	}
	
	/**
	 * @param \DateTime $lastPullCompletedAt
	 */
	public function setLastPullCompletedAt($lastPullCompletedAt)
	{
		$this->lastPullCompletedAt = $lastPullCompletedAt;
	}
	
	/**
	 * @return \DateTime
	 */
	public function getLastPullStartedAt()
	{
		return $this->lastPullStartedAt;
	}
	
	/**
	 * @param \DateTime $lastPullStartedAt
	 */
	public function setLastPullStartedAt($lastPullStartedAt)
	{
		$this->lastPullStartedAt = $lastPullStartedAt;
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
	
	/**
	 * @return \DateTime
	 */
	public function getUpdatedAt()
	{
		return $this->updatedAt;
	}
	
	/**
	 * @param \DateTime $updatedAt
	 */
	public function setUpdatedAt($updatedAt)
	{
		$this->updatedAt = $updatedAt;
	}
	
	/**
	 * @return mixed
	 */
	public function getPullInProgress()
	{
		return $this->pullInProgress;
	}
	
	/**
	 * @param mixed $pullInProgress
	 */
	public function setPullInProgress($pullInProgress)
	{
		$this->pullInProgress = $pullInProgress;
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
	public function getShouldPull()
	{
		return $this->shouldPull;
	}
	
	/**
	 * @param mixed $shouldPull
	 */
	public function setShouldPull($shouldPull)
	{
		$this->shouldPull = $shouldPull;
	}
	
	/**
	 * @return mixed
	 */
	public function getAllowPush()
	{
		return $this->allowPush;
	}
	
	/**
	 * @param mixed $allowPush
	 */
	public function setAllowPush($allowPush)
	{
		$this->allowPush = $allowPush;
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
	
	
	
	

}
