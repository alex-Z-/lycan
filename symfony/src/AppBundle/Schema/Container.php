<?php
namespace AppBundle\Schema;

use Doctrine\Common\Collections\ArrayCollection;

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
}