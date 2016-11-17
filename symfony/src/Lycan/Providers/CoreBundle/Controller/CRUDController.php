<?php
namespace Lycan\Providers\CoreBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Lycan\Providers\CoreBundle\Entity\BatchExecutions;

use Lycan\Providers\TabsBundle\API\Client\ApiClient as TabsClient;
use Lycan\Providers\TabsBundle\API\Client as TClient;



use Lycan\Providers\RentivoBundle\API\Client;
class CRUDController extends Controller
{
	
	public function executionsAction(){
		
		$id = $this->get('request')->get($this->admin->getIdParameter());
		
		$object = $this->admin->getObject($id);
		
		if (!$object) {
			throw new NotFoundHttpException(sprintf('not found object id : %s', $id));
		}
		
		//your code
		$em =  $this->container->get('doctrine')->getEntityManager();
		
		$logs = $em->getRepository('AppBundle:Log')->findBy(['batch' => $object->getId()]);
		
		return $this->render('CoreBundle:Admin/BatchExecutions:show_executions.html.twig', array(
			'action'   => 'action_name',
			'object' => $object,
			'logs' => $logs
			//more objects...
		));
		
	}
	
	public function goAction(){
		$debug = true;
		
		$object = $this->admin->getSubject();
		$factory = $this->container->get('lycan.provider.api.factory');
		$client = $factory->create(strtolower($object->getProviderName()), $object);
		$properties = $client->fetchAllProperties();
		dump($properties);die();
	}
	
	public function pullAction(){
		
		$object = $this->admin->getSubject();
		
		if (!$object) {
			throw new NotFoundHttpException('There is no provider available');
		}
		
		$em =  $this->container->get('doctrine')->getEntityManager();
		
		$object->setPullInProgress(true);
		$batch = new BatchExecutions();
		$batch->setProvider($object);
		$object->setLastActiveBatch( $batch );
		$em->persist($object);
		$em->persist($batch);
		$em->flush();
		$this->addFlash('sonata_flash_success', sprintf( 'Performing a Pull Syncronization for %s (%s) ', $object->getNickname(), $object->getProviderName() ));
		
		$logger = $this->container->get('app.logger.jobs');
		$logger->setBatch($batch->getId());
		$logger->debug("Creating a new batch execution job");
		
		
		$logger = $this->container->get('app.logger.user_actions')->logger;
		$logger->info(  'Manual initiation of pull syncronization', ['provider' => $object->getId(), "nickname" => $object->getNickname()] );
		
		// Add
		$msg = [ "id" => $object->getId(), "batch" => $batch->getId() ];
		$code = $this->admin->getCode();
		$provider = current( array_slice( explode( ".", $code ) , -1 ));
		if($provider !== strtolower($object->getProviderName())){
			throw new \Error("Mismatch on Provider Admin and Provider Name on Subject Entity");
		}
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