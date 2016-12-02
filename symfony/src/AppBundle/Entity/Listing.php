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
	 * @ORM\Column(type="string")
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
	

	
	
}
