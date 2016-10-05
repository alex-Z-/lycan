<?php
namespace Lycan\Providers\CoreBundle\Annotations;

/**
 * @Annotation
 * @Target("CLASS")
 */
class DiscriminatorEntry implements \Doctrine\ORM\Mapping\Annotation
{
	/**
	 *@var string
	 */
	public $discr;
}