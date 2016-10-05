<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use AppBundle\Entity\STI\ChannelBridge as ChannelBridge;
/**
 * @ORM\Entity
 */
class ChannelProperty extends ChannelBridge
{
	/**
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Property", inversedBy="channels")
	 * @ORM\JoinColumn(name="property_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
	 *
	 */
	private $property;
	
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
	public function getProperty()
	{
		return $this->property;
	}
	
	/**
	 * @param mixed $property
	 */
	public function setProperty($property)
	{
		$this->property = $property;
	}
	
	
	

}
