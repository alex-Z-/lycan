<?php
// Fungus/ShortyBundle/Helper/CSVTypes.php
namespace  AppBundle\Helper;

class CSVTypes {
	const PROVIDER_SUPERCONTROL             = 1;
	const PROVIDER_TABS             = 2;
	
	public static function getTypes() {
		return array(
			self::PROVIDER_TABS            => 'Provider Credentials - Tabs',
			self::PROVIDER_SUPERCONTROL            => 'Provider Credentials - Supercontrol',
		);
	}
	
	public static function getTypesAndIds() {
		$all=self::getTypes();
		$return=array();
		foreach($all as $key=>$value) {
			$return[]=array("id"=>$key,"title"=>$value);
		}
		return $return;
	}
	
	public static function getNameOfType($type) {
		$allTypes=self::getTypes();
		if (isset($allTypes[$type])) return $allTypes[$type];
		return " - Unknown Type -";
	}
	
	public static function getEntityClass($type) {
		
		switch ($type) {
			case self::PROVIDER_SUPERCONTROL:         return 'Lycan\Providers\SupercontrolBundle\Entity\ProviderSupercontrolAuth';
			case self::PROVIDER_TABS:         return 'Lycan\Providers\TabsBundle\Entity\ProviderTabsAuth';
			default: return false;
		}
	}
	
	public static function existsType($type) {
		$allTypes=self::getTypes();
		if (isset($allTypes[$type])) return true;
		return false;
	}
	
}