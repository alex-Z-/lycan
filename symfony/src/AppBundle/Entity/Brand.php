<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use AppBundle\Entity\Base\MappedSuperclassBase as Base;
/**
 * Property
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Brand extends Base
{
 
	/**
	 * @var \Ramsey\Uuid\Uuid
	 *
	 * @ORM\Id
	 * @ORM\Column(type="uuid")
	 * @ORM\GeneratedValue(strategy="CUSTOM")
	 * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
	 */
	protected $id;
	
	/**
	 * @ORM\Column(type="string")
	 */
	private $descriptiveName;
	
	/**
	 * @ORM\Column(type="string")
	 */
	private $brandName;
	
	
	/**
	 * Bidirectional - Many general features are owned by many properties (INVERSE SIDE)
	 *
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\ChannelBrand", cascade={"all"},  mappedBy="brand",  orphanRemoval=true)
	 */
	private $channels;
	
	/**
	 * Bidirectional - Many general features are owned by many properties (INVERSE SIDE)
	 *
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\UserBrandRegistry", cascade={"all"},  mappedBy="brand",  orphanRemoval=true)
	 */
	private $members;
	
	/**
	 * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Property", cascade={"persist"},  mappedBy="brands")
	 */
	private $properties;
	
	
	/**
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	private $moderateNewProperties;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $maxTotalProperties;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $maxTotalPropertiesPerMember;
	
	
	
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->users = new \Doctrine\Common\Collections\ArrayCollection();
		$this->properties = new \Doctrine\Common\Collections\ArrayCollection();
		$this->members = new \Doctrine\Common\Collections\ArrayCollection();
		$this->channels = new \Doctrine\Common\Collections\ArrayCollection();
	}
	
	function __toString()
	{
		return $this->getBrandName();
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
	public function getModerateNewProperties()
	{
		return $this->moderateNewProperties;
	}
	
	/**
	 * @param mixed $moderateNewProperties
	 */
	public function setModerateNewProperties($moderateNewProperties)
	{
		$this->moderateNewProperties = $moderateNewProperties;
	}
	
	/**
	 * @return mixed
	 */
	public function getMaxTotalProperties()
	{
		return $this->maxTotalProperties;
	}
	
	/**
	 * @param mixed $maxTotalProperties
	 */
	public function setMaxTotalProperties($maxTotalProperties)
	{
		$this->maxTotalProperties = $maxTotalProperties;
	}
	
	/**
	 * @return mixed
	 */
	public function getMaxTotalPropertiesPerMember()
	{
		return $this->maxTotalPropertiesPerMember;
	}
	
	/**
	 * @param mixed $maxTotalPropertiesPerMember
	 */
	public function setMaxTotalPropertiesPerMember($maxTotalPropertiesPerMember)
	{
		$this->maxTotalPropertiesPerMember = $maxTotalPropertiesPerMember;
	}
	
	
	/**
	 * @return mixed
	 */
	public function getDescriptiveName()
	{
		return $this->descriptiveName;
	}
	
	/**
	 * @param mixed $descriptiveName
	 */
	public function setDescriptiveName($descriptiveName)
	{
		$this->descriptiveName = $descriptiveName;
	}
	
	/**
	 * @return mixed
	 */
	public function getBrandName()
	{
		return $this->brandName;
	}
	
	/**
	 * @param mixed $brandName
	 */
	public function setBrandName($brandName)
	{
		$this->brandName = $brandName;
	}
	
	/**
	 * @return mixed
	 */
	public function getMembers()
	{
		return $this->members;
	}
	
	/**
	 * @param mixed $members
	 */
	public function setMembers($members)
	{
		$this->members = $members;
	}
	
	
	
	/**
     * Add user
     *
     * @param \Application\Sonata\UserBundle\Entity\User $user
     *
     * @return Brand
     */
    public function addMember(\AppBundle\Entity\UserBrandRegistry $user)
    {
        $this->members[] = $user;
		// $user->addBrand($this);
        return $this;
    }

    /**
     * Remove user
     *
     * @param \Application\Sonata\UserBundle\Entity\User $user
     */
    public function removeMember(\AppBundle\Entity\UserBrandRegistry $registry)
    {
		// $user->removeBrand($this);
        $this->members->removeElement($registry);
    }
	
	
	/**
	 * Add user
	 *
	 * @param \Application\Sonata\UserBundle\Entity\User $user
	 *
	 * @return Brand
	 */
	public function addChannel(\AppBundle\Entity\ChannelBrand $channel)
	{
		$this->channels[] = $channel;
		$channel->getBrand($this);
		return $this;
	}
	
	/**
	 * Remove user
	 *
	 * @param \Application\Sonata\UserBundle\Entity\User $user
	 */
	public function removeChannel(\AppBundle\Entity\ChannelBrand $channel)
	{
		// $user->removeBrand($this);
		$this->channel->removeElement($channel);
	}

	
	
	
	/**
     * Add property
     *
     * @param \AppBundle\Entity\Property $property
     *
     * @return Brand
     */
    public function addProperty(\AppBundle\Entity\Property $property)
    {
        $this->properties[] = $property;

        return $this;
    }

    /**
     * Remove property
     *
     * @param \AppBundle\Entity\Property $property
     */
    public function removeProperty(\AppBundle\Entity\Property $property)
    {
        $this->properties->removeElement($property);
    }

    /**
     * Get properties
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProperties()
    {
        return $this->properties;
    }
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getUsers()
	{
		return $this->users;
	}
	
	/**
	 * @param \Doctrine\Common\Collections\ArrayCollection $users
	 */
	public function setUsers($users)
	{
		$this->users = $users;
	}
	
	/**
	 * @return mixed
	 */
	public function getChannels()
	{
		return $this->channels;
	}
	
	/**
	 * @param mixed $channels
	 */
	public function setChannels($channels)
	{
		$this->channels = $channels;
	}
    
    
	

}
