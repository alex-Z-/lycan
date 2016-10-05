<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use AppBundle\Entity\Base\MappedSuperclassBase as Base;
/**
 * Property
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\PropertyRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Property extends Base
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
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $deletedAt;
	
	
	/**
	 * @ORM\Column(type="json")
	 */
	private $schemaObject;
	
	/**
	 * @ORM\Column(type="string")
	 */
	private $descriptiveName;

	/**
	 * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Brand", cascade={"persist","remove"}, inversedBy="properties")
	 * @ORM\JoinTable(name="property_brand_registry")
	 */
	private $brands;
	
	
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->brands = new \Doctrine\Common\Collections\ArrayCollection();
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
	public function getDescriptiveName()
	{
		return $this->descriptiveName;
	}
	
	/**
	 * @return mixed
	 */
	public function getSchemaObject()
	{
		return $this->schemaObject;
	}
	
	/**
	 * @param mixed $schemaObject
	 */
	public function setSchemaObject($schemaObject)
	{
		$this->schemaObject = $schemaObject;
	}
	
	/**
	 * @param mixed $descriptiveName
	 */
	public function setDescriptiveName($descriptiveName)
	{
		$this->descriptiveName = $descriptiveName;
	}
	
	/**
     * Add brand
     *
     * @param \AppBundle\Entity\Brand $brand
     *
     * @return Property
     */
    public function addBrand(\AppBundle\Entity\Brand $brand)
    {
        $this->brands[] = $brand;

        return $this;
    }

    /**
     * Remove brand
     *
     * @param \AppBundle\Entity\Brand $brand
     */
    public function removeBrand(\AppBundle\Entity\Brand $brand)
    {
        $this->brands->removeElement($brand);
    }

    /**
     * Get brands
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBrands()
    {
        return $this->brands;
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
