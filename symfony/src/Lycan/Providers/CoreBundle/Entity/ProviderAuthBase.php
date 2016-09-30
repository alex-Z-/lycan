<?php

namespace Lycan\Providers\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
 
/**
 * @ORM\MappedSuperclass()
 */
class ProviderAuthBase
{
	
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
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $nickname;
	
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
	
}
