<?php
namespace Lycan\Providers\CoreBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Incoming;
use Pristine\Schema\Container as SchemaContainer;
use Lycan\Providers\RentivoBundle\Incoming\Hydrator\SchemaHydrator as Hydrator;
use Lycan\Providers\RentivoBundle\Incoming\Transformer\RentivoTransformer;
use JsonSchema\SchemaStorage;
use JsonSchema\Validator;
use ListingSchema\Load;
use JsonSchema\Constraints\Factory;
use JsonSchema\Constraints\Constraint;


use Lycan\Providers\RentivoBundle\API\Client;
class CRUDController extends Controller
{
	public function pullAction(){
		$object = $this->admin->getSubject();
		
		if (!$object) {
			throw new NotFoundHttpException('There is no provider available');
		}
		
		$em =  $this->container->get('doctrine')->getEntityManager();
		$object->setPullInProgress(true);
		$em->persist($object);
		$em->flush();
		$this->addFlash('sonata_flash_success', sprintf( 'Performing a Pull Syncronization for %s', $object->getNickname() ));
		
		$logger = $this->container->get('app.logger.user_actions')->logger;
		$logger->info(  'Manual initiation of pull syncronization', ['provider' => $object->getId(), "nickname" => $object->getNickname()] );
		
		// Add
		$msg = [ "id" => $object->getId() ];
		$code = $this->admin->getCode();
		$provider = current( array_slice( explode( ".", $code ) , -1 ));
		
		$routingKey = sprintf("lycan.provider.%s", $provider);
		$this->container->get('lycan.rabbit.producer.pull_provider')->publish(serialize($msg), $routingKey);
			
		
		return new RedirectResponse($this->admin->generateUrl('list'));
		
		
	}
	
	public function pullStopAction(){
		$object = $this->admin->getSubject();
		
		if (!$object) {
			throw new NotFoundHttpException('There is no provider available');
		}
		
		$em =  $this->container->get('doctrine')->getEntityManager();
		$object->setPullInProgress(false);
		$em->persist($object);
		$em->flush();
		$this->addFlash('sonata_flash_info', sprintf( 'Stopped any running syncronizations for %s', $object->getNickname() ));
		
		$logger = $this->container->get('app.logger.user_actions')->logger;
		$logger->info(  'Manual initiation of stop syncronization', ['provider' => $object->getId(), "nickname" => $object->getNickname()] );
		
		return new RedirectResponse($this->admin->generateUrl('list'));
		
		
	}
	
	
	
}