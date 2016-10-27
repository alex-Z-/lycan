<?php
namespace Pristine\Schema;

use Pristine\Collections\ArrayCollection;
use Pristine\Utils\ArrayUtils;
class Container extends ArrayCollection {
	
	public function fromArray(array $array, $clear = false)
	{
		
		foreach ($array as $key => $val) {
			if (is_array($val)) {
				$t = new Self();
				$t->fromArray($val);
				$this->set($key, $t);
			} else {
				$this->set($key, $val);
			}
		}
		
		return $this;
	}
	
	public function toJson(){
		return json_encode($this->toArray(true), true);
	}
	
	public function toArray($recursive = true)
	{
		
		if ($recursive) {
			$result = [];
			if (!$this->isEmpty()) {
				
				foreach ($this->elements as $key => $value) {
					if ($value instanceof self) {
						$result[ $key ] = $value->toArray(true);
					} else {
						$result[ $key ] = $value;
					}
				}
			}
			return $result;
		} else {
			return $this->elements;
		}
	}
	
	public function set($key, $value)
	{
		// Check if has dot notation
		if( strpos( $key, '.') !== false){
			$values = $this->toArray();
			
			ArrayUtils::setNestedArrayValue($key, $value, $values);
			// We now need to return back the response...
			
			$container = new self();
			$container->fromArray($values);
			$this->clear();
			
			$this->elements = $container->elements;
			return null;
		}
		parent::set($key, $value);
	}
	
	
	public function get($name, $default = null)
	{
		
		// Check for dot notation.. and return null if it has a dot notiation..
		if( strpos( $name, '.') !== false){
			// We don't want to remove protected
			$s = $this->toArray(true);
			$f = ArrayUtils::getNestedArrayValue( $name, $s);
			return $f;
		}
		return $this->has($name)
			? $this->elements[ $name ]
			: $default;
	}
	
	public function has($name)
	{
		// Check for dot notation.. and return null if it has a dot notiation..
		if( strpos( $name, '.') !== false){
			// We don't want to remove protected
			$s = $this->toArray(true);
			$f = ArrayUtils::hasNestedArrayValue( $name, $s);
			return $f;
		}
		
		return isset($this->elements[ $name ]);
	}
	
	
}