<?php
namespace Pristine\Mapper;

use Pristine\Collections\LanguageCollection;
use Pristine\Language\Levo;
use Pristine\Language\MatcherInterface;
use Pristine\Language\Stem\PorterStemmer;
use Pristine\Language\StringDistance;

class Mapping {
	
	const FULL = 1;
	const STEM = 2;
	/**
	 * Mapping constructor.
	 */
	private $mode = self::FULL;
	private $_enumClass;
	private $_enums;
	private $_mappings;
	public $lastMatch = [];
	private $_costVersusScore = [
		1 => 0.7,
		2 => 0.55,
		3 => 0.5,
		4 => 0.42,
		5 => 0.35,
		6 => 0.3,
		7 => 0.24,
		8 => 0.24,
		9 => 0.22,
		10 => 0.2
	];
	private $tolerance = null;
	private $_matchOnStems = true;
	private $_explodeOnMappings = true;
	private $_matcher;
	
	public function __construct($enumClass, $mapping )
	{
	
		$this->_enumClass = $enumClass;
		$reflectorEnums = new \ReflectionClass($this->_enumClass);
		$this->_enums = $reflectorEnums->getConstants();
		
		$this->_mappings = $mapping->getMap();
		
		$this->setMatcher( new Levo() );
		
	}
	
	/**
	 * @return mixed
	 */
	public function getEnumClass()
	{
		return $this->_enumClass;
	}
	
	/**
	 * @param mixed $enumClass
	 */
	public function setEnumClass($enumClass)
	{
		$this->_enumClass = $enumClass;
	}
	
	/**
	 * @return array
	 */
	public function getCostsVersusScore()
	{
		return $this->_costsVersusScore;
	}
	
	/**
	 * @param array $costsVersusScore
	 */
	public function setCostsVersusScore($costsVersusScore)
	{
		$this->_costsVersusScore = $costsVersusScore;
	}
	
	
	
	/**
	 * @return array
	 */
	public function getEnums()
	{
		return $this->_enums;
	}
	
	/**
	 * @param array $enums
	 */
	public function setEnums($enums)
	{
		$this->_enums = $enums;
	}
	
	/**
	 * @return mixed
	 */
	public function getMappings()
	{
		return $this->_mappings;
	}
	
	/**
	 * @param mixed $mappings
	 */
	public function setMappings($mappings)
	{
		$this->_mappings = $mappings;
	}
	
	
	
	public function setMatcher( $_matcher){
		$this->_matcher = $_matcher;
	}
	
	/**
	 * @return mixed
	 */
	public function getMatcher()
	{
		return $this->_matcher;
	}
	
	/**
	 * @param mixed $mode
	 */
	public function setMode($mode)
	{
		$this->mode = $mode;
	}
	
	/**
	 * @return mixed
	 */
	public function getMode()
	{
		return $this->mode;
	}
	
	public function map($string){
		
		// Given this string.. we need to find out what the best match is.
		// RESET IT
		$this->setLastMatch(null);
		
		$match = $this->_getBestMatch($string);
		if(!$match && $this->_matchOnStems){
			// If we have failed on the matching on the main, we can also try and match on the stems.
			$this->setMode( self::STEM );
			$match = $this->_getBestMatch($string);
		}
		
		// Last ditch. Basically we now try and explode on the mappings.
		if(!$match && $this->_explodeOnMappings){
			
		}
		
		if($match){
			$this->setLastMatch($match);
			return $match->get("enum");
		}
		return null;
		
	}
	
	
	
	private function _getBestMatch($string){
		
		// $stem = PorterStemmer::Stem("Luxurious"); Luxuri
		// $jaro = StringDistance::JaroWinkler($string, "Barn");
		// $value = $levo->similarity($string, "Farmhouse" );
		$collection = new LanguageCollection();
		$mappings = $this->getMappings();
		
		// $string = "St. Caravan";
		
		if($this->getMode() === self::STEM){
			$string = PorterStemmer::stem($string);
		}
	
		foreach($mappings as $key => $value){
			
			if($this->getMode() === self::STEM){
				$key = PorterStemmer::stem($key);
			}
			
			$result = $this->getMatcher()->match($string, $key);
			
			$item = new LanguageCollection();
			$item->set("source", $string);
			$item->set("string", $key);
			$item->set("enum", $value);
			$item->set("result", $result);
			$collection->add($item);
		}
		
		$exact = $collection->filter(function ($a) {
			
			if($a->get("result")->getSimilar()){
				return true;
			} else {
				return false;
			}
			
		});
		
		
		// We only have one result. Great. Let's roll with it.
		if(!$exact->isEmpty() && $exact->count() === 1){
			return $exact->first();
		}
	
		// So now, let's filter any items which have an awful score.
		$best = $collection->filter(function ($a) {
			if($a->get("result")->getScore() > $this->getCostTolerance( $a->get("result")->getCost() ) ){
				return true;
			} else {
				return false;
			}
		});
		
		if($best->count() > 1){
			// Need to sort by descending..
			$iterator = $best->getIterator();
			$iterator->uasort(function ($a, $b) {
				// Baically, sort by score, but then fall back on cost, and then finally string length.
				if($a->get("result")->getScore() === $b->get("result")->getScore() ){
					// if($a->get("string") === $b->get("string") ){
					if($a->get("result")->getCost() === $b->get("result")->getCost()){
						return ( strlen($a->get("string")) < strlen($b->get("string"))) ? -1 : 1;
					} else {
						return ($a->get("result")
								->getCost() < $b->get("result")
								->getCost()) ? 1 : -1;
					}
					
				} else {
					return ($a->get("result")
							->getScore() < $b->get("result")
							->getScore()) ? 1 : -1;
				}
			});
			$best = new LanguageCollection(iterator_to_array($iterator));
			// Return the first.
		
		}
		
		return $best->first();
		
	}
	
	public function getCostTolerance($cost)
	{
		
		if($this->getTolerance()){
			return $this->getTolerance();
		}
		// Otherise use cost versus tolerance score
		return isset($this->_costVersusScore[$cost]) ? $this->_costVersusScore[$cost] : 0.2;
	}
	
	/**
	 * @return array
	 */
	public function getLastMatch()
	{
		return $this->lastMatch;
	}
	
	/**
	 * @return null
	 */
	public function getTolerance()
	{
		return $this->tolerance;
	}
	
	/**
	 * @param null $tolerance
	 */
	public function setTolerance($tolerance)
	{
		$this->tolerance = $tolerance;
	}
	
	
	
	/**
	 * @param array $lastMatch
	 */
	public function setLastMatch($lastMatch)
	{
		$this->lastMatch = $lastMatch;
	}
	
	
	
}