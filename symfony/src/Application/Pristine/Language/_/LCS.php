<?php
/**
 * Created by IntelliJ IDEA.
 * User: layke
 * Date: 19/10/16
 * Time: 14:07
 */

namespace Pristine\Language;

/**
 * The longest common subsequence (LCS) problem consists in finding the
 * longest subsequence common to two (or more) sequences. It differs from
 * problems of finding common substrings: unlike substrings, subsequences are
 * not required to occupy consecutive positions within the original sequences.
 *
 * Used by the diff utility, by Git for reconciling multiple changes, etc.
 */
class LCS
{
	private $C = array();
	private $X = "";
	private $Y = "";
	
	public function __construct($str1, $str2) {
		
		$this->X = $str1;
		$this->Y = $str2;
		
		$m = strlen($str1);
		$n = strlen($str2);
		
		$this->C = array();
		
		for ($i = 0; $i <= $m; $i++) {
			$this->C[$i][0] = 0;
		}
		
		for ($j = 0; $j <= $n; $j++) {
			$this->C[0][$j] = 0;
		}
		
		for ($i = 1; $i <= $m; $i++) {
			for ($j = 1; $j <= $n; $j++) {
				if ($str1[$i-1] == $str2[$j-1]) {
					$this->C[$i][$j] = $this->C[$i-1][$j-1] + 1;
					
				} else {
					$this->C[$i][$j] = max($this->C[$i][$j-1], $this->C[$i-1][$j]);
				}
			}
		}
	
	}
	
	public function length() {
		return $this->C[strlen($this->X)][strlen($this->Y)];
	}
	
	public function __toString() {
		return $this->value();
	}
	
	public function value() {
		return $this->backtrack(strlen($this->X), strlen($this->Y));
	}
	
	/**
	 * Edit distance when only insertion and deletion is allowed (no
	 * substitution)
	 * = strlen(str1) + strlen(str2) - 2 * length(LCS(str1, str2))
	 * @param type $string1
	 * @param type $string2
	 */
	public function distance() {
		return strlen($this->X) + strlen($this->Y) - 2 * $this->length();
	}
	
	
	private function backtrack($i, $j) {
		if ($i == 0 || $j == 0) {
			return "";
		}
		
		if ($this->X[$i-1] == $this->Y[$j-1]) {
			return $this->backtrack($i-1, $j-1) . $this->X[$i-1];
		}
		
		if ($this->C[$i][$j-1] > $this->C[$i-1][$j]) {
			return $this->backtrack($i, $j-1);
		}
		
		return $this->backtrack($i-1, $j);

	}

}