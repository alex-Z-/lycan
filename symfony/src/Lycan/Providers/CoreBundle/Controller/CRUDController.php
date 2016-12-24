<?php
namespace Lycan\Providers\CoreBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Lycan\Providers\CoreBundle\Entity\BatchExecutions;

use Lycan\Providers\TabsBundle\API\Client\ApiClient as TabsClient;
use Lycan\Providers\TabsBundle\API\Client as TClient;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;

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
	
	public function pushAction(){
		
		$object = $this->admin->getSubject();
		
		if (!$object) {
			throw new NotFoundHttpException('There is no provider available');
		}
		
		$em =  $this->container->get('doctrine')->getEntityManager();
		
		$object->setPushInProgress(true);
		$batch = new BatchExecutions();
		$batch->setProvider($object->getProvider());
		$object->setLastActiveBatch( $batch );
		$em->persist($object);
		$em->persist($batch);
		$em->flush();
		$this->addFlash('sonata_flash_success', sprintf( 'Performing a Push Syncronization for %s (%s) ', $object->getProvider()->getNickname(), $object->getProvider()->getProviderName() ));
		
		$logger = $this->container->get('app.logger.jobs');
		$logger->setBatch($batch->getId());
		$logger->debug("Creating a new batch execution job");
	
		$logger = $this->container->get('app.logger.user_actions')->logger;
		$logger->info(  'Manual initiation of push syncronization', ['channel' => (string)  $object->getId(), "nickname" => $object->getProvider()->getNickname()] );
		
		// Add
		$msg = [ "id" => (string) $object->getId(), "batch" => (string) $batch->getId() ];
		$provider = strtolower($object->getProvider()->getProviderName());
		$routingKey = sprintf("lycan.provider.push.brand.%s", $provider);
		$this->container->get('lycan.rabbit.producer.push_brand')->publish(serialize($msg), $routingKey);
		
		return new RedirectResponse($this->admin->generateUrl('list'));
		
		
	}
	
	
	public function pushStopAction(){
		$object = $this->admin->getSubject();
		
		if (!$object) {
			throw new NotFoundHttpException('There is no provider available');
		}
		
		$em =  $this->container->get('doctrine')->getEntityManager();
		$object->setPushInProgress(false);
		$em->persist($object);
		$em->flush();
		$this->addFlash('sonata_flash_info', sprintf( 'Stopped any running syncronizations for %s', $object->getProvider()->getNickname() ));
		
		$logger = $this->container->get('app.logger.user_actions')->logger;
		$logger->info(  'Manual initiation of stop syncronization', ['id' => $object->getId(), "nickname" => $object->getProvider()->getNickname()] );
		
		return new RedirectResponse($this->admin->generateUrl('list'));
		
		
	}
	
	
	public function pullAction(){
		
		$object = $this->admin->getSubject();
		
		if (!$object) {
			throw new NotFoundHttpException('There is no provider available');
		}
		
		$providerKey = strtolower( $object->getProviderName() );
		
		$manager = $this->container->get('lycan.provider.manager.factory')->create($providerKey);
		if(is_null($manager)){
			throw new \Exception("Manager API Factory did not return a Valid Provider Manager.");
		}
		$manager->setProvider($object);
		$invokable = $manager->getQueuePullProviderClosure();
		call_user_func($invokable, $object);
		$this->addFlash('sonata_flash_success', sprintf('Performing a Pull Syncronization for %s (%s) ', $object->getNickname(), $object->getProviderName()));
		
		
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
	
	
	/**
	 * @param ProxyQueryInterface $selectedModelQuery
	 * @param Request             $request
	 *
	 * @return RedirectResponse
	 */
	public function batchActionPull(ProxyQuery $selectedModelQuery, Request $request = null)
	{
		if (!$this->admin->isGranted('EDIT') || !$this->admin->isGranted('DELETE')) {
			throw new AccessDeniedException();
		}
		
		$modelManager = $this->admin->getModelManager();
		$selectedModels = $selectedModelQuery->execute();
		
		$invalidCredentials = [];
		try {
			foreach ($selectedModels as $object) {
				$providerKey = strtolower( $object->getProviderName() );
				$manager = $this->container->get('lycan.provider.manager.factory')->create($providerKey);
				if(is_null($manager)){
					throw new \Exception("Manager API Factory did not return a Valid Provider Manager.");
				}
				if($object->getIsValidCredentials()) {
					$manager->setProvider($object);
					$invokable = $manager->getQueuePullProviderClosure();
					call_user_func($invokable, $object);
				} else {
					$invalidCredentials[] = $object;
				}
			}
			
			// $modelManager->update($selectedModel);
		} catch (\Exception $e) {
			$this->addFlash('sonata_flash_error', 'There was an error while attempting to batch pull providers.');
			
			return new RedirectResponse(
				$this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters()))
			);
		}
		
		$this->addFlash('sonata_flash_success', sprintf('The selected (%d) providers have been queued to be pulled.', count($selectedModels) - count($invalidCredentials)) );
		if(!empty($invalidCredentials)){
			$this->addFlash('sonata_flash_error', sprintf('There were (%d) provider(s) which had invalid credentials. Please validate them before pulling.', count($invalidCredentials)) );
		}
		return new RedirectResponse(
			$this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters()))
		);
	}
	
	/**
	 * @param ProxyQueryInterface $selectedModelQuery
	 * @param Request             $request
	 *
	 * @return RedirectResponse
	 */
	public function batchActionPullStop(ProxyQuery $selectedModelQuery, Request $request = null)
	{
		if (!$this->admin->isGranted('EDIT') || !$this->admin->isGranted('DELETE')) {
			throw new AccessDeniedException();
		}
		
		$selectedModels = $selectedModelQuery->execute();
		$em =  $this->container->get('doctrine')->getManager();
		
		try {
			foreach ($selectedModels as $object) {
				$object->setPullInProgress(false);
				$em->persist($object);
			}
			
			// $modelManager->update($selectedModel);
		} catch (\Exception $e) {
			$this->addFlash('sonata_flash_error', 'There was an error while attempting to stop the batch pull of providers.');
			$this->addFlash('sonata_flash_error', $e->getMessage());
			
			return new RedirectResponse(
				$this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters()))
			);
		}
		$em->flush();
		$this->addFlash('sonata_flash_success', sprintf('The selected (%d) providers have been stopped for pulling.', count($selectedModels)) );
		
		return new RedirectResponse(
			$this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters()))
		);
	}
	
	
	public function batchActionValidateCredentials(ProxyQuery $selectedModelQuery, Request $request = null)
	{
		
		$selectedModels = $selectedModelQuery->execute();
		$em =  $this->container->get('doctrine')->getManager();
		
		try {
			foreach ($selectedModels as $object) {
				
				$providerKey = strtolower( $object->getProviderName() );
				$client = $this->container->get('lycan.provider.api.factory')->create($providerKey, $object);
				try {
					$ponged = $client->ping();
					if ($ponged) {
						$object->setIsValidCredentials(true);
					} else {
						$object->setIsValidCredentials(false);
					}
				} catch(\Exception $e){
					$object->setIsValidCredentials(false);
				}
				$em->persist($object);
			}
			
			// $modelManager->update($selectedModel);
		} catch (\Exception $e) {
			$this->addFlash('sonata_flash_error', 'There was an error while attempting to validate the credentials.');
			$this->addFlash('sonata_flash_error', $e->getMessage());
			$em->flush();
			return new RedirectResponse(
				$this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters()))
			);
		}
		$em->flush();
		$this->addFlash('sonata_flash_success', sprintf('The selected (%d) providers have been validated.', count($selectedModels)) );
		
		return new RedirectResponse(
			$this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters()))
		);
	}
	
	
	
	
	
	
	
}