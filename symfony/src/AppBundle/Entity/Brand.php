<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * Property
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Brand
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
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	private $moderateNewProperties;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $maxTotalProperties;
	
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
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $maxTotalPropertiesPerMember;
	
	/**
	 * @var \DateTime $created
	 *
	 * @Gedmo\Timestampable(on="create")
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $createdAt;
	
	/**
	 * @var \DateTime $updated
	 *
	 * @Gedmo\Timestampable(on="update")
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $updatedAt;
	
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
	 * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User",  cascade={"persist"})
	 * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
	 */
	private $owner;
	
	
	
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
     * Constructor
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
		$this->properties = new \Doctrine\Common\Collections\ArrayCollection();
		$this->members = new \Doctrine\Common\Collections\ArrayCollection();
    }
	
	function __toString()
	{
		return $this->getBrandName();
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
     * Set owner
     *
     * @param \Application\Sonata\UserBundle\Entity\User $owner
     *
     * @return Brand
     */
    public function setOwner(\Application\Sonata\UserBundle\Entity\User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \Application\Sonata\UserBundle\Entity\User
     */
    public function getOwner()
    {
        return $this->owner;
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
	

}
