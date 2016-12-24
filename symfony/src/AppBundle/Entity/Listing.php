<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use AppBundle\Entity\Base\MappedSuperclassBase as Base;
/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\ListingRepository")
 */
class Listing extends Property
{
	
	/**
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	private $arePoliciesValid;
	
	/**
	 * @ORM\Column(type="json")
	 */
	private $policiesErrors;
	
	/**
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ChannelBrand", inversedBy="listings", fetch="EXTRA_LAZY")
	 * @ORM\JoinColumn(name="channel_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
	 *
	 */
	private $channel;
	
	
	
	/**
	 * @return mixed
	 */
	public function getArePoliciesValid()
	{
		return $this->arePoliciesValid;
	}
	
	/**
	 * @param mixed $arePoliciesValid
	 */
	public function setArePoliciesValid($arePoliciesValid)
	{
		$this->arePoliciesValid = $arePoliciesValid;
	}
	
	/**
	 * @return mixed
	 */
	public function getPoliciesErrors()
	{
		return $this->policiesErrors;
	}
	
	/**
	 * @param mixed $policiesErrors
	 */
	public function setPoliciesErrors($policiesErrors)
	{
		$this->policiesErrors = $policiesErrors;
	}
	
	
	public function getPoliciesErrorsJson(){
		return json_encode( $this->getPoliciesErrors(), true);
	}
	
	/**
	 * @return mixed
	 */
	public function getChannel()
	{
		return $this->channel;
	}
	
	/**
	 * @param mixed $channel
	 */
	public function setChannel($channel)
	{
		$this->channel = $channel;
	}
	
	
	public function getMasterOwnerUsername(){
		return $this->getMaster()->getOwner()->getUsername();
	}
	
	public function getMasterOwnerEmail(){
		return $this->getMaster()->getOwner()->getEmail();
	}
	
	public function getMasterProviderListingId(){
		return $this->getMaster()->getProviderListingId();
	}
	
	public function getMasterProvider(){
		return (string) $this->getMaster()->getProvider();
	}


	
}
