<?php

namespace AppBundle\Monolog\Handler;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use AppBundle\Entity\Log;
/**
 * Stores to database
 *
 */
namespace AppBundle\Monolog\Processor;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bridge\Monolog\Processor\WebProcessor;

class JobProcessor extends WebProcessor
{
	private $_session;
	private $_batch;
	private $_eventGroup;
	
	public function __construct(Session $session)
	{
		$this->_session = $session;
	}
	
	public function setBatch($batch){
		$this->_batch = $batch;
		return $this;
	}
	
	public function setEventGroup($eventGroup){
		$this->_eventGroup = $eventGroup;
		return $this;
	}
	
	public function getEventGroup(){
		return $this->_eventGroup;
	}
	
	public function processRecord(array $record)
	{
		
		$record['input'] = isset($record['context']['input']) ? $record['context']['input'] : null;
		$record['output'] = isset($record['context']['output'])? $record['context']['output'] : null;
		
		unset($record['context']['output'], $record['context']['input']);
		
		if(isset($record['context'])){
			$record['extra']['context'] = $record['context'];
		}
		
		if(isset($this->_batch)){
			$record['extra']['batch'] = (String) $this->_batch;
		}
		if(isset($this->_eventGroup)){
			$record['extra']['eventGroup'] = (String) $this->_eventGroup;
		}
		
		$record['extra']['serverData'] = "";
		
		if( is_array($this->serverData) ) {
			foreach ($this->serverData as $key => $value) {
				
				if( is_array($value) ) {
					$value = print_r($value, true);
				}
				
				$record['extra']['serverData'] .= $key . ": " . $value . "\n";
			}
		}
		
		foreach ($_SERVER as $key => $value) {
			
			if( is_array($value) ) {
				$value = print_r($value, true);
			}
			
			$record['extra']['serverData'] .= $key . ": " . $value . "\n";
		}
		
		return $record;
	}
}