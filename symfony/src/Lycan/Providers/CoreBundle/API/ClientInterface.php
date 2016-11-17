<?php
namespace Lycan\Providers\CoreBundle\API;
use Lycan\Providers\CoreBundle\Entity\ProviderAuthBase;

interface ClientInterface {
	public static function getInstance();
	public function setAuthProvider(ProviderAuthBase  $auth);
	public function getClient();
	public function setClient($client);
	public function get();
	public function fetchAllListings();
	public function getListingFull($id);
	
	
}