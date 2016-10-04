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
	
	
	public function getDeletedAt()
	{
		return $this->deletedAt;
	}
	
	public function setDeletedAt($deletedAt)
	{
		$this->deletedAt = $deletedAt;
	}
}
