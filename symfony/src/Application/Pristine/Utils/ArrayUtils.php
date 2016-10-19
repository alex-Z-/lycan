<?php
namespace Pristine\Utils;
class ArrayUtils
{
	
	/**
	 * @param array $array
	 * @param string $prefix
	 * @return array
	 */
	public static function flattenArray(array $array, $prefix = '')
	{
		$result = [];
		
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$result = array_merge($result, self::flattenArray($value, $prefix . $key . '.'));
			} else {
				$result[ $prefix . $key ] = $value;
			}
		}
		
		return $result;
	}
	
	// This will reverse the flattenArray
	public static function explodeFlatArray($flat) {
		$result = array();
		foreach($flat as $key => $val) {
			$keyParts = explode(".", $key);
			$currentArray = &$result;
			for($i=0; $i<count($keyParts)-1; $i++) {
				if(!isSet($currentArray[$keyParts[$i]])) {
					$currentArray[$keyParts[$i]] = array();
				}
				$currentArray = &$currentArray[$keyParts[$i]];
			}
			$currentArray[$keyParts[count($keyParts)-1]] = $val;
		}
		return $result;
	}
	
	
	public static function unsetObjects(&$array)
	{
		
		foreach ($array as $key => &$value) {
			
			if (is_array($value)) {
				self::unsetObjects($value);
			} else {
				if ($value instanceof \DateTime) {
					$array[ $key ] = $value->format("M d Y");
				} else {
					if (is_object($value)) {
						unset($array[ $key ]);
					}
				}
			}
		}
	}
	
	
	/**
	 * @param string $key
	 * @param array $array
	 * @param null $default
	 * @return array
	 */
	public static function getNestedArrayValue($key, $array, $default = null)
	{
		if (is_null($key)) {
			return $array;
		}
		
		if (isset($array[ $key ])) {
			return $array[ $key ];
		}
		
		foreach (explode('.', $key) as $segment) {
			if (!is_array($array) || !array_key_exists($segment, $array)) {
				return $default;
			}
			
			$array = $array[ $segment ];
		}
		
		return $array;
	}
	
	
	/**
	 * @param string $key
	 * @param array $array
	 * @return bool
	 */
	public static function hasNestedArrayValue($key, $array)
	{
		if (empty($array) || is_null($key)) {
			return false;
		}
		
		if (array_key_exists($key, $array)) {
			return true;
		}
		
		foreach (explode('.', $key) as $segment) {
			if (!is_array($array) || !array_key_exists($segment, $array)) {
				return false;
			}
			
			$array = $array[ $segment ];
		}
		
		return true;
	}
	
	
	/**
	 * @param string $key
	 * @param mixed $value
	 * @param array $array
	 * @return array mixed
	 */
	public static function setNestedArrayValue($key, $value, &$array)
	{
		if (is_null($key)) {
			return $array = $value;
		}
		
		$keys = explode('.', $key);
		
		while (count($keys) > 1) {
			$key = array_shift($keys);
			
			// If the key doesn't exist at this depth, we will just create an empty array
			// to hold the next value, allowing us to create the arrays to hold final
			// values at the correct depth. Then we'll keep digging into the array.
			
			if (!isset($array[ $key ]) || !is_array($array[ $key ])) {
				$array[ $key ] = [];
			}
			
			$array =& $array[ $key ];
		}
		
		$array[ array_shift($keys) ] = $value;
		
		return $array;
	}
	
	
	/**
	 * @param string|array $keys
	 * @param array $array
	 */
	public static function removeNestedArrayKey($keys, &$array)
	{
		$original =& $array;
		
		foreach ((array)$keys as $key) {
			$parts = explode('.', $key);
			
			while (count($parts) > 1) {
				
				$part = array_shift($parts);
				
				if (isset($array[ $part ]) && is_array($array[ $part ])) {
					$array =& $array[ $part ];
				}
				
			}
			
			unset($array[ array_shift($parts) ]);
			
			// clean up after each pass
			$array =& $original;
		}
	}
	
	
	/**
	 * @param string $column
	 * @param array $entities
	 * @return array
	 */
	public static function getEntityColumnArray($column, array $entities = [])
	{
		$ids = [];
		
		foreach ($entities as $entity) {
			
			$method = sprintf('get%s', ucfirst($column));
			
			if (method_exists($entity, $method)) {
				$ids[] = $entity->$method();
			}
		}
		
		return $ids;
	}
	
	
	public static function mergeArrays(array $array1, $array2 = null)
	{
		if (is_array($array2)) {
			foreach ($array2 as $key => $val) {
				if (is_array($array2[ $key ])) {
					$array1[ $key ] = (array_key_exists($key, $array1) && is_array($array1[ $key ]))
						? self::mergeArrays($array1[ $key ], $array2[ $key ])
						: $array2[ $key ];
				} else {
					$array1[ $key ] = $val;
				}
			}
		}
		
		return $array1;
	}
	
}