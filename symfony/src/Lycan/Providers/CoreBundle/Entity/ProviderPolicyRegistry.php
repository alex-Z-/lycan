<?php

namespace Lycan\Providers\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * Property
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ProviderPolicyRegistry
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
	 * @ORM\ManyToOne(targetEntity="Lycan\Providers\CoreBundle\Entity\ProviderAuthBase", inversedBy="policies",cascade={"persist"})
	 * @ORM\JoinColumn(name="provider_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
	 *
	 */
	private $provider;
	
	
	
	/**
	 * @ORM\ManyToOne(targetEntity="Lycan\Providers\CoreBundle\Entity\Policy", inversedBy="policies",cascade={"persist"})
	 * @ORM\JoinColumn(name="policy_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
	 */
	private $policy;
	
	/**
	 * @return mixed
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
	public function getPolicy()
	{
		return $this->policy;
	}
	
	/**
	 * @param mixed $policy
	 */
	public function setPolicy($policy)
	{
		$this->policy = $policy;
	}
	
	
	
}
