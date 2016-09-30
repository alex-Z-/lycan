<?php

namespace AppBundle\Subscriber;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
class TablePrefixSubscriber implements \Doctrine\Common\EventSubscriber
{
	protected $prefix = '';
	
	public function __construct($prefix)
	{
		$this->prefix = (string) $prefix;
	}
	
	public function getSubscribedEvents()
	{
		return array('loadClassMetadata');
	}
	
	public function loadClassMetadata(LoadClassMetadataEventArgs $args)
	{
		$classMetadata = $args->getClassMetadata();
		if ($classMetadata->isInheritanceTypeSingleTable() && !$classMetadata->isRootEntity()) {
			// if we are in an inheritance hierarchy, only apply this once
			return;
		}
	
		if(FALSE !== strpos( $classMetadata->namespace, "AppBundle" ) || FALSE !== strpos( $classMetadata->namespace, "Lycan" )  ){
			if (false === strpos($classMetadata->getTableName(), $this->prefix)) {
				$tableName = $this->prefix . $classMetadata->getTableName();
				$classMetadata->setPrimaryTable(['name' => $tableName]);
			}
		
			foreach ($classMetadata->getAssociationMappings() as $fieldName => $mapping) {
				
				if ($mapping['type'] == \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_MANY
					&& array_key_exists('name', $classMetadata->associationMappings[$fieldName]['joinTable']) ) {     // Check if "joinTable" exists, it can be null if this field is the reverse side of a ManyToMany relationship
					$mappedTableName = $classMetadata->associationMappings[$fieldName]['joinTable']['name'];
					$classMetadata->associationMappings[$fieldName]['joinTable']['name'] = $this->prefix . $mappedTableName;
				}
			}
			
		}
		
		
	}
}