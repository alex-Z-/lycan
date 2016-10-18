<?php
// src/AppBundle/Entity/Message.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use FOS\MessageBundle\Entity\Message as BaseMessage;

/**
 * @ORM\Entity
 */
class Message extends BaseMessage
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\ManyToOne(
	 *   targetEntity="AppBundle\Entity\Thread",
	 *   inversedBy="messages"
	 * )
	 * @var \FOS\MessageBundle\Model\ThreadInterface
	 */
	protected $thread;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User",  cascade={"persist"})
	 * @var \FOS\MessageBundle\Model\ParticipantInterface
	 */
	protected $sender;
	
	/**
	 * @ORM\OneToMany(
	 *   targetEntity="AppBundle\Entity\MessageMetadata",
	 *   mappedBy="message",
	 *   cascade={"all"}
	 * )
	 * @var MessageMetadata[]|Collection
	 */
	protected $metadata;
}