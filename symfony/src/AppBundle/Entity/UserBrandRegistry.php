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
class UserBrandRegistry
{
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
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	
	/**
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Brand", inversedBy="members",fetch="EXTRA_LAZY")
	 * @ORM\JoinColumn(name="brand_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
	 *
	 */
	private $brand;
	
	
	
	/**
	 * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="brands",fetch="EXTRA_LAZY")
	 * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
	 */
	private $member;
	
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
	 * @return mixed
	 */
	public function getMember()
	{
		return $this->member;
	}
	
	/**
	 * @param mixed $member
	 */
	public function setMember($member)
	{
		$this->member = $member;
	}
	
	public function __toString()
	{
		return (string) $this->getMember();
	}
	
}
