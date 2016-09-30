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

/**
 * Property
 *
 * @ORM\Table("fos_user_user")
 * @ORM\Entity
 */
class User extends BaseUser
{
	/**
	 * @var \Ramsey\Uuid\Uuid
	 *
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Brand", cascade={"persist"}, inversedBy="users")
	 * @ORM\JoinTable(name="lycan_user_brand_registry")
	 */
	private $brands;
	
	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $notes;
	
	
	public function __construct()
	{
		parent::__construct();
		$this->addRole("ROLE_USER");
		$this->brands = new ArrayCollection();
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
