<?php

namespace Lycan\Providers\CoreBundle\Entity;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity
 */
class Policy
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
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	private $isPublic;
	
	
	/**
	 * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User",  cascade={"persist"})
	 * @ORM\JoinColumn(name="owner_id", referencedColumnName="id",  onDelete="CASCADE")
	 */
	private $owner;
	
	/**
	 * @ORM\Column(type="json")
	 */
	private $policySchema;
	
	/**
	 * Bidirectional - Many general features are owned by many properties (INVERSE SIDE)
	 *
	 * @ORM\OneToMany(targetEntity="Lycan\Providers\CoreBundle\Entity\ProviderPolicyRegistry", mappedBy="policy" )
	 */
	private $policies;
	
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
	public function getIsPublic()
	{
		return $this->isPublic;
	}
	
	/**
	 * @param mixed $isPublic
	 */
	public function setIsPublic($isPublic)
	{
		$this->isPublic = $isPublic;
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
	public function getPolicySchema()
	{
		return $this->policySchema;
	}
	
	/**
	 * @param mixed $policySchema
	 */
	public function setPolicySchema($policySchema)
	{
		$this->policySchema = $policySchema;
	}
	
	
	
	/**
	 * @return mixed
	 */
	public function getPolicies()
	{
		return $this->policies;
	}
	
	/**
	 * @param mixed $policies
	 */
	public function setPolicies($policies)
	{
		$this->policies = $policies;
	}
	
	public function __toString()
	{
		return $this->getDescriptiveName();
	}
	
}