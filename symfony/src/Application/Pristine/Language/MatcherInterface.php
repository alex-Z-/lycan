<?php
/**
 * Created by IntelliJ IDEA.
 * User: layke
 * Date: 19/10/16
 * Time: 15:38
 */

namespace Pristine\Language;


interface MatcherInterface {
	public function match($string1, $string2, $opts = []);
}