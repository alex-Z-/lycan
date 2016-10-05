<?php
namespace Lycan\Providers\CoreBundle\Mapping;

/**
 * This Listener listens to the loadClassMetadata event. Upon this event
 * it hooks into Doctrine to update discriminator maps. Adding entries
 * to the discriminator map at parent level is just not nice. We turn this
 * around with this mechanism. In the subclass you will be able to give an
 * entry for the discriminator map. In this listener we will retrieve the
 * load metadata event to update the parent with a good discriminator map,
 * collecting all entries from the subclasses.
 */
class DiscriminatorListener implements \Doctrine\Common\EventSubscriber {

	// The driver of Doctrine, can be used to find all loaded classes
	private $driver;

	// The *temporary* map used for one run, when computing everything
	private $map;

	// The cached map, this holds the results after a computation, also for other classes
	private $cachedMap;

	const ENTRY_ANNOTATION      = 'Lycan\Providers\CoreBundle\Annotations\DiscriminatorEntry';

	public function getSubscribedEvents() {
		return Array( \Doctrine\ORM\Events::loadClassMetadata );
	}

	public function __construct( \Doctrine\ORM\EntityManager $db ) {
		$this->driver       = $db->getConfiguration()->getMetadataDriverImpl();
		$this->cachedMap    = Array();
	}

	public function loadClassMetadata( \Doctrine\ORM\Event\LoadClassMetadataEventArgs $event ) {
		// Reset the temporary calculation map and get the classname
		$this->map  = Array();
		$class      = $event->getClassMetadata()->name;

		if(!(new \ReflectionClass($class))->isInstantiable()){
			return;
		}

		// Did we already calculate the map for this element?
		if( array_key_exists( $class, $this->cachedMap ) ) {
			$this->overrideMetadata( $event, $class );
			return;
		}

		// Do we have to process this class?
		if( $this->extractEntry( $class ) ) {
			// Now build the whole map
			$this->checkFamily( $class );
		} else {
			// Nothing to do…
			return;
		}

		// Create the lookup entries
		$dMap = array_flip( $this->map );
		foreach( $this->map as $cName => $discr ) {
			$this->cachedMap[$cName]['map'] = $dMap;;
		}

		// Override the data for this class
		$this->overrideMetadata( $event, $class );
	}

	private function overrideMetadata( \Doctrine\ORM\Event\LoadClassMetadataEventArgs $event, $class ) {
		// Set the discriminator map and value
		$event->getClassMetadata()->setDiscriminatorMap($this->cachedMap[$class]['map']);
	}

	private function checkFamily( $class ) {
		$parentClass = (new \ReflectionClass( $class ))->getParentClass();

		if( $parentClass !== false && (new \ReflectionClass($parentClass->name))->isInstantiable()) {
			// Also check all the children of our parent
			$this->checkFamily( $parentClass->name );
		} else {
			// This is the top-most parent, used in overrideMetadata
			$this->cachedMap[$class]['isParent'] = true;

			// Find all the children of this class
			$this->checkChildren( $class );
		}
	}

	private function checkChildren( $class ) {
		foreach( $this->driver->getAllClassNames() as $name ) {
			$parentClass = (new \ReflectionClass( $class ))->getParentClass();

			if(!$parentClass){
				continue;
			}

			$cParent = $parentClass->name;

			// Haven't done this class yet? Go for it.
			if( !array_key_exists( $name, $this->map ) && $cParent == $class && $this->extractEntry( $name ) ) {
				$this->checkChildren( $name );
			}
		}
	}

	private function extractEntry( $class ) {

		$annotationReader = new \Doctrine\Common\Annotations\AnnotationReader();
		$annotation = $annotationReader->getClassAnnotation(new \ReflectionClass($class), self::ENTRY_ANNOTATION);

		if(empty($annotation)){
			return false;
		}

		$discr = $annotation->discr;

		if( in_array($discr , $this->map ) ) {
			throw new \Exception( "Found duplicate discriminator map entry '" . $discr . "' in " . $class );
		}

		$this->map[$class] = $discr;
		return true;
	}

}