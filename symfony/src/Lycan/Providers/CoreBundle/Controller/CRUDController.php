<?php
namespace Lycan\Providers\CoreBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use AppBundle\Exception\NoBrandFoundException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use AppBundle\AppBundle;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Incoming;
use AppBundle\Schema\Container as SchemaContainer;
use Lycan\Providers\RentivoBundle\Incoming\Hydrator\SchemaHydrator;
use Lycan\Providers\RentivoBundle\Incoming\Transformer\RentivoTransformer;

class CRUDController extends Controller
{
	public function pullAction(){
		$object = $this->admin->getSubject();
		
		if (!$object) {
			throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
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
		
		//////
		
		$client   = $this->container->get('guzzle.client.rentivo');
		$response = $client->get('/api/public/properties/schemas/52021');
		$result = json_decode ( (string) $response->getBody(), true );
		$data = $result['data'];
		$incoming = new Incoming\Processor(  new RentivoTransformer() );
		
		$schema = $incoming->process(
			$data,
			new SchemaContainer(),
			new SchemaHydrator()
		);
		dump($schema->toArray());
		die();
		
		
		////
		
		
		
		return new RedirectResponse($this->admin->generateUrl('list'));
		
		
	}
	
	public function pullStopAction(){
		$object = $this->admin->getSubject();
		
		if (!$object) {
			throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
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