<?php
namespace Lycan\Providers\CoreBundle\Annotations;
use Doctrine\Common\Annotations\AnnotationReader as AnnotationReader;


Annotation::$reader = new AnnotationReader();
// Annotation::$reader->setDefaultAnnotationNamespace( __NAMESPACE__ . "" );

class Annotation  {
	public static $reader;
	public static function getAnnotationForClass( $className ) {
		
		$class = new \ReflectionClass( $className );
		
		$annotations =  Annotation::$reader->getClassAnnotations( $class );
		return $annotations;
	}
}
