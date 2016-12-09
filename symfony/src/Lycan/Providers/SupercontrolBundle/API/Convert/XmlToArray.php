<?php
namespace Lycan\Providers\SupercontrolBundle\API\Convert;

/**
 * An easy way to convert xml to php array.
 *
 * @link https://github.com/P54l0m5h1k/XML-to-Array-PHP/
 * @link https://github.com/P54l0m5h1k/Array-to-XML-PHP/
 */
class XmlToArray
{
	/**
	 * Parsing XML into array.
	 *
	 * @static
	 *
	 * @param string $contents      string containing XML
	 * @param bool   $getAttributes
	 * @param bool   $tagPriority   priority of values in the array - `true` if the higher priority in the tag,
	 * `false` if only the attributes needed
	 * @param string $encoding      target XML encoding
	 *
	 * @return array
	 */
	public static function x2a($contents, $getAttributes = true, $tagPriority = true, $encoding = 'utf-8')
	{
		$contents = trim($contents);
		if (empty($contents)) {
			return [];
		}
		$parser = xml_parser_create('');
		xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, $encoding);
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		if (xml_parse_into_struct($parser, $contents, $xmlValues) === 0) {
			xml_parser_free($parser);
			
			return [];
		}
		xml_parser_free($parser);
		if (empty($xmlValues)) {
			return [];
		}
		unset($contents, $parser);
		$xmlArray = [];
		$current = &$xmlArray;
		$repeatedTagIndex = [];
		foreach ($xmlValues as $num => $xmlTag) {
			$result = null;
			$attributesData = null;
			if (isset($xmlTag['value'])) {
				if ($tagPriority) {
					$result = $xmlTag['value'];
				} else {
					$result['value'] = $xmlTag['value'];
				}
			}
			if (isset($xmlTag['attributes']) and $getAttributes) {
				foreach ($xmlTag['attributes'] as $attr => $val) {
					if ($tagPriority) {
						$attributesData[$attr] = $val;
					} else {
						$result['@attributes'][$attr] = $val;
					}
				}
			}
			if ($xmlTag['type'] == 'open') {
				$parent[$xmlTag['level'] - 1] = &$current;
				if (!is_array($current) or (!in_array($xmlTag['tag'], array_keys($current)))) {
					$current[$xmlTag['tag']] = $result;
					unset($result);
					if ($attributesData) {
						$current['@'.$xmlTag['tag']] = $attributesData;
					}
					$repeatedTagIndex[$xmlTag['tag'].'_'.$xmlTag['level']] = 1;
					$current = &$current[$xmlTag['tag']];
				} else {
					if (isset($current[$xmlTag['tag']]['0'])) {
						$current[$xmlTag['tag']][$repeatedTagIndex[$xmlTag['tag'].'_'.$xmlTag['level']]] = $result;
						unset($result);
						if ($attributesData) {
							if (isset($repeatedTagIndex['@'.$xmlTag['tag'].'_'.$xmlTag['level']])) {
								$current[$xmlTag['tag']][$repeatedTagIndex['@'.$xmlTag['tag'].'_'.$xmlTag['level']]] = $attributesData;
							}
						}
						$repeatedTagIndex[$xmlTag['tag'].'_'.$xmlTag['level']] += 1;
					} else {
						$current[$xmlTag['tag']] = [$current[$xmlTag['tag']], $result];
						unset($result);
						$repeatedTagIndex[$xmlTag['tag'].'_'.$xmlTag['level']] = 2;
						if (isset($current['@'.$xmlTag['tag']])) {
							$current[$xmlTag['tag']]['@0'] = $current['@'.$xmlTag['tag']];
							unset($current['@'.$xmlTag['tag']]);
						}
						if ($attributesData) {
							$current[$xmlTag['tag']]['@1'] = $attributesData;
						}
					}
					$lastItemIndex = $repeatedTagIndex[$xmlTag['tag'].'_'.$xmlTag['level']] - 1;
					$current = &$current[$xmlTag['tag']][$lastItemIndex];
				}
			} elseif ($xmlTag['type'] == 'complete') {
				if (!isset($current[$xmlTag['tag']]) and empty($current['@'.$xmlTag['tag']])) {
					$current[$xmlTag['tag']] = $result;
					unset($result);
					$repeatedTagIndex[$xmlTag['tag'].'_'.$xmlTag['level']] = 1;
					if ($tagPriority and $attributesData) {
						$current['@'.$xmlTag['tag']] = $attributesData;
					}
				} else {
					if (isset($current[$xmlTag['tag']]['0']) and is_array($current[$xmlTag['tag']])) {
						$current[$xmlTag['tag']][$repeatedTagIndex[$xmlTag['tag'].'_'.$xmlTag['level']]] = $result;
						unset($result);
						if ($tagPriority and $getAttributes and $attributesData) {
							$current[$xmlTag['tag']]['@'.$repeatedTagIndex[$xmlTag['tag'].'_'.$xmlTag['level']]] = $attributesData;
						}
						$repeatedTagIndex[$xmlTag['tag'].'_'.$xmlTag['level']] += 1;
					} else {
						$current[$xmlTag['tag']] = [
							$current[$xmlTag['tag']],
							$result,
						];
						unset($result);
						$repeatedTagIndex[$xmlTag['tag'].'_'.$xmlTag['level']] = 1;
						if ($tagPriority and $getAttributes) {
							if (isset($current['@'.$xmlTag['tag']])) {
								$current[$xmlTag['tag']]['@0'] = $current['@'.$xmlTag['tag']];
								unset($current['@'.$xmlTag['tag']]);
							}
							if ($attributesData) {
								$current[$xmlTag['tag']]['@'.$repeatedTagIndex[$xmlTag['tag'].'_'.$xmlTag['level']]] = $attributesData;
							}
						}
						$repeatedTagIndex[$xmlTag['tag'].'_'.$xmlTag['level']] += 1;
					}
				}
			} elseif ($xmlTag['type'] == 'close') {
				$current = &$parent[$xmlTag['level'] - 1];
			}
			unset($xmlValues[$num]);
		}
		
		return $xmlArray;
	}
	
	public static function xmlToArray($xml, $options = array()) {
		$defaults = array(
			'namespaceSeparator' => ':',//you may want this to be something other than a colon
			'attributePrefix' => '@',   //to distinguish between attributes and nodes with the same name
			'alwaysArray' => array(),   //array of xml tag names which should always become arrays
			'autoArray' => true,        //only create arrays for tags which appear more than once
			'textContent' => '$',       //key used for the text content of elements
			'autoText' => true,         //skip textContent key if node has no attributes or child nodes
			'keySearch' => false,       //optional search and replace on tag and attribute names
			'keyReplace' => false       //replace values for above search values (as passed to str_replace())
		);
		$options = array_merge($defaults, $options);
		$namespaces = $xml->getDocNamespaces();
		$namespaces[''] = null; //add base (empty) namespace
		
		//get attributes from all namespaces
		$attributesArray = array();
		foreach ($namespaces as $prefix => $namespace) {
			foreach ($xml->attributes($namespace) as $attributeName => $attribute) {
				//replace characters in attribute name
				if ($options['keySearch']) $attributeName =
					str_replace($options['keySearch'], $options['keyReplace'], $attributeName);
				$attributeKey = $options['attributePrefix']
								. ($prefix ? $prefix . $options['namespaceSeparator'] : '')
								. $attributeName;
				$attributesArray[$attributeKey] = (string)$attribute;
			}
		}
		
		//get child nodes from all namespaces
		$tagsArray = array();
		foreach ($namespaces as $prefix => $namespace) {
			foreach ($xml->children($namespace) as $childXml) {
				//recurse into child nodes
				$childArray = self::xmlToArray($childXml, $options);
				list($childTagName, $childProperties) = each($childArray);
				
				//replace characters in tag name
				if ($options['keySearch']) $childTagName =
					str_replace($options['keySearch'], $options['keyReplace'], $childTagName);
				//add namespace prefix, if any
				if ($prefix) $childTagName = $prefix . $options['namespaceSeparator'] . $childTagName;
				
				if (!isset($tagsArray[$childTagName])) {
					//only entry with this key
					//test if tags of this type should always be arrays, no matter the element count
					$tagsArray[$childTagName] =
						in_array($childTagName, $options['alwaysArray']) || !$options['autoArray']
							? array($childProperties) : $childProperties;
				} elseif (
					is_array($tagsArray[$childTagName]) && array_keys($tagsArray[$childTagName])
														   === range(0, count($tagsArray[$childTagName]) - 1)
				) {
					//key already exists and is integer indexed array
					$tagsArray[$childTagName][] = $childProperties;
				} else {
					//key exists so convert to integer indexed array with previous value in position 0
					$tagsArray[$childTagName] = array($tagsArray[$childTagName], $childProperties);
				}
			}
		}
		
		//get text content of node
		$textContentArray = array();
		$plainText = trim((string)$xml);
		if ($plainText !== '') $textContentArray[$options['textContent']] = $plainText;
		
		//stick it all together
		$propertiesArray = !$options['autoText'] || $attributesArray || $tagsArray || ($plainText === '')
			? array_merge($attributesArray, $tagsArray, $textContentArray) : $plainText;
		
		//return node as array
		return array(
			$xml->getName() => $propertiesArray
		);
	}
	
	
}