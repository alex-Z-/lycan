<?php
namespace Lycan\Providers\CoreBundle\Exception;

class PolicyException extends \Exception
{
	protected $_schema;
	protected $_errors;
	public function __construct($message="", $code=0 , Exception $previous=NULL, $schema = NULL, $errors = [])
	{
		$this->_schema = $schema;
		$this->_errors = $errors;
		parent::__construct($message, $code, $previous);
	}
	public function getSchema()
	{
		return $this->_schema;
	}
	
	/**
	 * @return array
	 */
	public function getErrors()
	{
		return $this->_errors;
	}
	
	
}