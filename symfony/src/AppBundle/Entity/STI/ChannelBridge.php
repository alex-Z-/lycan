<?php

namespace AppBundle\Entity\STI;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use AppBundle\Entity\Base\MappedSuperclassBase as Base;
/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"property" = "AppBundle\Entity\ChannelProperty", "brand" = "AppBundle\Entity\ChannelBrand"})
 */
abstract class ChannelBridge extends Base
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
	
	
	
	
	
}
