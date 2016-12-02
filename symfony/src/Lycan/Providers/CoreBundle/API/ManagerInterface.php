<?php
namespace Lycan\Providers\CoreBundle\API;

interface ManagerInterface {

	public function getQueuePullListingsClosure();
	public function getQueuePushListingsClosure();
	public function getProcessIncomingMappingClosure();
	public function getProcessOutgoingMappingClosure();
	
}