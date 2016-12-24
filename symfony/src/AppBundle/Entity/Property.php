<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use AppBundle\Entity\Base\MappedSuperclassBase as Base;
/**
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"property" = "Property", "listing" = "Listing"})
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\PropertyRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Property
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
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\Listing", mappedBy="master", cascade={"all"}, orphanRemoval=true, fetch="EXTRA_LAZY")
	 */
	private $listings;
	
	/**
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Property", inversedBy="listings", fetch="EXTRA_LAZY")
	 * @ORM\JoinColumn(name="master_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	private $master;
	
	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $deletedAt;
	
	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $syncedAt;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User",  cascade={"persist"}, inversedBy="properties", fetch="EXTRA_LAZY")
	 * @ORM\JoinColumn(name="owner_id", referencedColumnName="id" , onDelete="CASCADE")
	 */
	private $owner;
	
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
	 * @Gedmo\Timestampable(on="change", field={"descriptiveName", "schemaObject"})
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $updatedAt;
	
	/**
	 * @ORM\Column(type="json")
	 */
	private $schemaObject;
	
	/**
	 * @ORM\Column(type="array", nullable=true)
	 */
	private $sourceDataMapping;
	
	/**
	 * @ORM\Column(type="string")
	 */
	private $descriptiveName;

	/**
	 * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Brand", cascade={"persist","remove"}, inversedBy="properties", fetch="EXTRA_LAZY")
	 * @ORM\JoinTable(name="property_brand_registry")
	 */
	private $brands;
	
	
	/**
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	private $isSchemaValid;
	
	/**
	 * @ORM\Column(type="json")
	 */
	private $schemaErrors;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Lycan\Providers\CoreBundle\Entity\ProviderAuthBase", inversedBy="properties", fetch="EXTRA_LAZY")
	 * @ORM\JoinColumn(name="provider_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
	 *
	 */
	private $provider;
	
	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $providerListingId;
	
	
	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $providerPublicURL;

	
	/**
	 * @return mixed
	 */
	public function getProviderPublicURL()
	{
		return $this->providerPublicURL;
	}
	
	/**
	 * @param mixed $providerPublicURL
	 */
	public function setProviderPublicURL($providerPublicURL)
	{
		$this->providerPublicURL = $providerPublicURL;
	}
	
	
	/**
     * Constructor
     */
    public function __construct()
    {
        $this->brands = new \Doctrine\Common\Collections\ArrayCollection();
		$this->brand = new \Doctrine\Common\Collections\ArrayCollection();
		$this->isSchemaValid = false;
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
	
	/**
	 * @return mixed
	 */
	public function getIsSchemaValid()
	{
		return $this->isSchemaValid;
	}
	
	/**
	 * @param mixed $isSchemaValid
	 */
	public function setIsSchemaValid($isSchemaValid)
	{
		$this->isSchemaValid = $isSchemaValid;
	}
	
	/**
	 * @return mixed
	 */
	public function getSchemaErrors()
	{
		return $this->schemaErrors;
	}
	
	/**
	 * @param mixed $schemaErrors
	 */
	public function setSchemaErrors($schemaErrors)
	{
		$this->schemaErrors = $schemaErrors;
	}
	
	public  function __toString()
	{
			return $this->getDescriptiveName();
	}
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getBrand()
	{
		return $this->brand;
	}
	
	/**
	 * @param \Doctrine\Common\Collections\ArrayCollection $brand
	 */
	public function setBrand($brand)
	{
		$this->brand = $brand;
	}
	
	/**
	 * @return \Lycan\Providers\CoreBundle\Entity\ProviderAuthBase|null
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
	public function getProviderListingId()
	{
		return $this->providerListingId;
	}
	
	/**
	 * @param mixed $providerListingId
	 */
	public function setProviderListingId($providerListingId)
	{
		$this->providerListingId = $providerListingId;
	}
	
	/**
	 * @return mixed
	 */
	public function getSyncedAt()
	{
		return $this->syncedAt;
	}
	
	/**
	 * @param mixed $syncedAt
	 */
	public function setSyncedAt($syncedAt)
	{
		$this->syncedAt = $syncedAt;
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
	public function getCreatedAt()
	{
		return $this->createdAt;
	}
	
	/**
	 * @param mixed $createdAt
	 */
	public function setCreatedAt($createdAt)
	{
		$this->createdAt = $createdAt;
	}
	
	/**
	 * @return mixed
	 */
	public function getUpdatedAt()
	{
		return $this->updatedAt;
	}
	
	/**
	 * @param mixed $updatedAt
	 */
	public function setUpdatedAt($updatedAt)
	{
		$this->updatedAt = $updatedAt;
	}
	
	/**
	 * @return mixed
	 */
	public function getListings()
	{
		return $this->listings;
	}
	
	/**
	 * @param mixed $listings
	 */
	public function setListings($listings)
	{
		$this->listings = $listings;
	}
	
	
	
	
	public function addListing(\AppBundle\Entity\Property $property)
	{
		$this->listings[] = $property;
		return $this;
	}
	
	/**
	 * @return mixed
	 */
	public function getMaster()
	{
		return $this->master;
	}
	
	/**
	 * @param mixed $master
	 */
	public function setMaster($master)
	{
		$this->master = $master;
	}
	
	/**
	 * @return mixed
	 */
	public function getSourceDataMapping()
	{
		return $this->sourceDataMapping;
	}
	
	public function setSourceDataMappingJson($json){
		$this->setSourceDataMapping(json_decode($json));
	}
	
	public function getSourceDataMappingJson() {
		return json_encode($this->sourceDataMapping);
	}
	
	/**
	 * @param mixed $sourceDataMapping
	 */
	public function setSourceDataMapping($sourceDataMapping)
	{
		
		// We only want to store upto a max length of 5 and we don't want any attribute greater than a depth of ZERO to
		// have any more than 40 elements.
		$this->sourceDataMapping = [];
		
		$sourceDataMapping = json_decode(json_encode($sourceDataMapping, 2), 2);
		
		$this->sourceDataMapping = $sourceDataMapping;
	}
	
	private function _fix(&$arr, $n = 0) {
		foreach ($arr as &$item) {
			dump($item);die();
			if (is_array($item) && $n <= 5 ) {
				$item = $this->_fix(array_slice($item, 0,  10 ), $n + 1);
				die();
			}
		}
		return $arr;
	}

	// used for exporting
	public function ownerUsername(){
		dump($this->getOwner());die();
		return $this->getOwner()->getUsername();
	}
	public function getOwnerEmail(){
		return $this->getOwner()->getEmail();
	}
	

	
	
}
