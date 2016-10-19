<?php
/**
 * Created by IntelliJ IDEA.
 * User: layke
 * Date: 19/10/16
 * Time: 13:59
 */

namespace Pristine\Matcher;


<?php

	/**
	 * Improved Levenshtein Distance using Phonemes
	 *
	 * A improved method for comparing the similarity of two strings by breaking down the strings into their base phonemes and having a variable tolerance for similarity.
	 *
	 * @package        	CodeIgniter
	 * @subpackage    	Libraries
	 * @category    	Libraries
	 * @author        	Wyatt Ferguson
	 * @license         http://www.opensource.org/licenses/mit-license.php
	 * @link 			https://github.com/wyattferguson/Improved-Levenshtein-Distance
	 */

	class Levo{
		private $_tolerance;
		private $_sort;
		
		function __construct($tolerance=0.2, $sort=TRUE){
			$this->set_tolerance($tolerance);
			$this->set_sort($sort);
		}
		
		
		/**
		 * Set fault tolerance for what is considered 'similar'.
		 *
		 * @param float $tol a percentage setting how close the strings need to be to be considered similar.
		 **/
		public function set_tolerance($tol=0.20){
			if($tol < 0 || $tol > 1) return FALSE;
			$this->_tolerance = round($tol,2);
		}
		
		
		/**
		 * Set whether the strings to compare should be sorted alphabetically.
		 *
		 * @param bool $sort TRUE sorts the strings, FALSE doesnt.
		 **/
		public function set_sort($sort=TRUE){
			$this->_sort = $sort;
		}
		
		
		/**
		 * Gets sorted boolean, whether the strings to compare should be sorted alphabetically.
		 *
		 * @return bool
		 **/
		public function get_sort(){
			return $this->_sort;
		}
		
		
		/**
		 * Gets fault tolerance for what is considered 'similar'.
		 *
		 * @return float
		 **/
		public function get_tolerance(){
			return $this->_tolerance;
		}
		
		
		/**
		 * Compare 2 strings to see how similar they are.
		 *
		 * @param string $str The first string to compare. Max length of 255 chars.
		 * @param string $cmp The second string to comapre against the first. Max length of 255 chars.
		 * @return mixed false if $str or $cmp is empty or longer then 255 chars, the max length for a PHP levenshtein.
		 **/
		public function similarity($str=NULL,$cmp=NULL){
			if (empty($str) || empty($cmp)) return FALSE;
			if (strlen($str) > 255 || strlen($cmp) > 255) return FALSE;
			
			$processed_str 	= $this->phoneme($str);
			$processed_cmp 	= $this->phoneme($cmp);
			$score 			= levenshtein ($processed_str,$processed_cmp,1,1,1);
			
			$avg_length 	= (strlen($processed_str) + strlen($processed_cmp)) / 2;
			$final_score 	= round((1.0 / $avg_length) * $score,2);
			
			if($final_score < $this->_tolerance){
				$grade = 1;
			}else{
				$grade = 0;
			}
			
			$data = array(
				'cost'		=>	$score,
				'score'		=> 	1 - $final_score,
				'similar'	=>	$grade,
			);
			return $data;
		}
		
		
		/**
		 * Transform a given string into its phoneme equivalent.
		 *
		 * @param string $str The string to be transformed in phonemes.
		 * @return string Phoneme string.
		 **/
		public function phoneme($str=''){
			$parts = explode(' ', $str);
			$phonemes = array();
			foreach($parts as $p){
				$phon = metaphone($p);
				array_push($phonemes, $phon);
			}
			if($this->_sort){
				sort($phonemes);
			}
			$str = implode(' ', $phonemes);
			return $str;
		}
	}