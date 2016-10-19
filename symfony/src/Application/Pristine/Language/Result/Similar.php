<?php
/**
 * Created by IntelliJ IDEA.
 * User: layke
 * Date: 19/10/16
 * Time: 15:50
 */

namespace Pristine\Language\Result;


class Similar {
	
	private $cost;
	private $score;
	private $similar;
	
	public function __construct($data)
	{
		if(!isset($data['score'])){
			throw new Exception("Result from Language\Matcher expected to receive a score, but none was provided");
		}
		
		$this->setScore($data['score']);
		
		if(isset($data['cost'])){
			$this->setCost($data['cost']);
		}
		
		if(isset($data['similar'])){
			$this->setSimilar($data['similar']);
		}
	}
	
	/**
	 * @return mixed
	 */
	public function getCost()
	{
		return $this->cost;
	}
	
	/**
	 * @param mixed $cost
	 */
	public function setCost($cost)
	{
		$this->cost = $cost;
	}
	
	/**
	 * @return mixed
	 */
	public function getScore()
	{
		return $this->score;
	}
	
	/**
	 * @param mixed $score
	 */
	public function setScore($score)
	{
		$this->score = $score;
	}
	
	/**
	 * @return mixed
	 */
	public function getSimilar()
	{
		return $this->similar;
	}
	
	/**
	 * @param mixed $similar
	 */
	public function setSimilar($similar)
	{
		$this->similar = $similar;
	}
	
	
}