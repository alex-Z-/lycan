<?php

/**
 * This file is part of the <name> project.
 *
 * (c) <yourname> <youremail>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Sonata\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sonata\UserBundle\Entity\BaseUser as BaseUser;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;
use FOS\MessageBundle\Model\ParticipantInterface;
/**
 * Property
 *
 * @ORM\Table("fos_user_user")
 * @ORM\Entity
 */
class User extends BaseUser implements ParticipantInterface
{
	/**
	 * @var \Ramsey\Uuid\Uuid
	 * @ORM\Id
	 * @ORM\Column(type="uuid")
	 * @ORM\GeneratedValue(strategy="CUSTOM")
	 * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
	 */
	protected $id;
	
	/**
	 * Bidirectional - Many general features are owned by many properties (INVERSE SIDE)
	 *
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\UserBrandRegistry", mappedBy="member" , fetch="EXTRA_LAZY")
	 */
	private $brands;
	
	/**
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\Property", mappedBy="owner", cascade={"all"}, orphanRemoval=true, fetch="EXTRA_LAZY")
	 */
	private $properties;
	
	/**
	 * @return mixed
	 */
	public function getProperties()
	{
		return $this->properties;
	}
	
	/**
	 * @param mixed $properties
	 */
	public function setProperties($properties)
	{
		$this->properties = $properties;
	}
	
	
	
	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $notes;
	
	public function getPropertiesCount(){
		return count($this->getProperties());
	}
	
	public function addd($what){
		// dump($what);die();
	}
	
	
	public function __construct()
	{
		parent::__construct();
		$this->addRole("ROLE_USER");
		$this->addRole("ROLE_LYCAN_OWNER");
		$this->brands = new ArrayCollection();
	}
	
	public function getLogValues(){
		return [
			"id" => (string) $this->getId(),
			"username" => $this->getUsername(),
			"email"	=> $this->getEmail()
		];
	}
	
	/**
	 * @return mixed
	 */
	public function getBrands()
	{
		return $this->brands;
	}
	
	/**
	 * @param mixed $brands
	 */
	public function setBrands($brands)
	{
		$this->brands = $brands;
	}
	
	/**
	 * @return mixed
	 */
	public function getNotes()
	{
		return $this->notes;
	}
	
	/**
	 * @param mixed $notes
	 */
	public function setNotes($notes)
	{
		$this->notes = $notes;
	}
	

    /**
     * Get id
     *
     * @return int $id
     */
    public function getId()
    {
        return $this->id;
    }
    
    
    

    /**
     * Add brand
     *
     * @param \AppBundle\Entity\Brand $brand
     *
     * @return User
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
    
   
}
