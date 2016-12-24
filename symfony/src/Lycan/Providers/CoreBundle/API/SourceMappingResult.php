<?php
/**
 * Created by IntelliJ IDEA.
 * User: layke
 * Date: 18/12/16
 * Time: 15:40
 */

namespace Lycan\Providers\CoreBundle\API;

// This contains the source data and the mapped result for the provider.
use Pristine\Schema\Container;

class SourceMappingResult {
	protected $source;
	protected $schema;
	
	public function __construct(array $source, Container $schema)
	{
		$this->setSource($source);
		$this->setSchema($schema);
	}
	
	/**
	 * @return mixed
	 */
	public function getSource()
	{
		return $this->source;
	}
	
	/**
	 * @param mixed $source
	 */
	public function setSource($source)
	{
		$this->source = $source;
	}
	
	/**
	 * @return mixed
	 */
	public function getSchema()
	{
		return $this->schema;
	}
	
	/**
	 * @param mixed $schema
	 */
	public function setSchema($schema)
	{
		$this->schema = $schema;
	}
	
	
	
	
}