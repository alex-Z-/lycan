<?php
/**
 * Created by IntelliJ IDEA.
 * User: layke
 * Date: 09/11/16
 * Time: 12:31
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 *
 * @ORM\Entity
 *
 */
/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\Table(name="log")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"log" = "Log", "event" = "Lycan\Providers\CoreBundle\Entity\Event"})
 */
class Log
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;
	
	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $log;
	
	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $context;
	

	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $serverData;
	
	
	
	
	
	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $level;
	
	/**
	 * @ORM\Column(type="datetime")
	 */
	private $modifiedAt;
	
	/**
	 * @ORM\Column(type="datetime")
	 */
	private $createdAt;
	
	/**
	 * @ORM\PreUpdate
	 */
	public function setModifiedValue()
	{
		$this->modifiedAt = new \DateTime();
	}
	
	/**
	 * @ORM\PrePersist
	 */
	public function setCreatedValue()
	{
		$this->modifiedAt = new \DateTime();
		
		$this->createdAt = new \DateTime();
	}
	
	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * Set log
	 *
	 * @param string $log
	 * @return SystemLog
	 */
	public function setLog($log)
	{
		$this->log = $log;
		
		return $this;
	}
	
	/**
	 * Get log
	 *
	 * @return string
	 */
	public function getLog()
	{
		return $this->log;
	}
	
	/**
	 * Set serverData
	 *
	 * @param string $serverData
	 * @return SystemLog
	 */
	public function setServerData($serverData)
	{
		$this->serverData = $serverData;
		
		return $this;
	}
	
	/**
	 * Get serverData
	 *
	 * @return string
	 */
	public function getServerData()
	{
		return $this->serverData;
	}
	
	/**
	 * Set level
	 *
	 * @param string $level
	 * @return SystemLog
	 */
	public function setLevel($level)
	{
		$this->level = $level;
		
		return $this;
	}
	
	/**
	 * Get level
	 *
	 * @return string
	 */
	public function getLevel()
	{
		return $this->level;
	}
	
	/**
	 * Set modified
	 *
	 * @param \DateTime $modified
	 * @return SystemLog
	 */
	public function setModifiedAt($modified)
	{
		$this->modifiedAt = $modified;
		
		return $this;
	}
	
	/**
	 * Get modified
	 *
	 * @return \DateTime
	 */
	public function getModifiedAt()
	{
		return $this->modifiedAt;
	}
	
	/**
	 * Set created
	 *
	 * @param \DateTime $created
	 * @return SystemLog
	 */
	public function setCreatedAt($created)
	{
		$this->createdAt = $created;
		
		return $this;
	}
	
	/**
	 * Get created
	 *
	 * @return \DateTime
	 */
	public function getCreatedAt()
	{
		return $this->createdAt;
	}
	

	
	/**
	 * @return mixed
	 */
	public function getContext()
	{
		return $this->context;
	}
	
	/**
	 * @param mixed $context
	 */
	public function setContext($context)
	{
		$this->context = $context;
		
		return $this;
	}
	
	public function __toString()
	{
		return $this->getLog();
	}
	
	
}