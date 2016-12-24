<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Listing;
use Lycan\Providers\CoreBundle\Consumer\PullProviderConsumer;
use PhpAmqpLib\Message\AMQPMessage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use React\Promise\Deferred;
use Lycan\Providers\CoreBundle\Consumer\PullListingConsumer;
use Lycan\Providers\CoreBundle\Consumer\PushListingConsumer;
class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepageer")
     */
    public function indexAction(Request $request)
    {
	
		try {
			$logger = $this->container->get('app.logger.jobs');
			
			$logger->crit("OKAY");
		} catch(\Exception $e){
			dump($e);die();
		}
		
		

		die("XXXXXX Test Index Homepage: " . uniqid());
		// $this->render("AppBundle::custom_layout.html.twig");
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
        ));
    }
	
	/**
	 * @Route("/pull_provider", name="pull_provider")
	 */
	public function pullProviderAction(Request $request)
	{
		
		
		$consumer = new PullProviderConsumer(
			$this->container->get('app.logger.jobs'),
			$this->container->get('doctrine')->getManager()
		);
		$consumer->setContainer($this->container);
		// CHANGE THIS ID TO THE PROVIDER YOU ARE ATTEMPTING TO PUSH.
		$i = ["id"=> 43, "batch" => '24beb663-30b6-4f60-934e-f0d8ed044a95' ];
		$msg = new AMQPMessage(serialize($i));
		
		$consumer->execute($msg);
		
		die("Pull Provider: " . uniqid());
		
	}
	
	/**
	 * @Route("/push", name="homepage")
	 */
	public function pushAction(Request $request)
	{
		
		die();
		$consumer = new PushListingConsumer(
			$this->container->get('app.logger.jobs'),
			$this->container->get('doctrine')->getManager()
		);
		$consumer->setContainer($this->container);
		
		$i = ["id"=>'6ea2d2e6-add0-4110-9062-dfd942891832', "provider" => 222, "batch" => '24beb663-30b6-4f60-934e-f0d8ed044a95', "jobsInBatch" => 33, "jobIndex" => 1 ];
		$msg = new AMQPMessage(serialize($i));
		
		$consumer->execute($msg);
		
		
		die("Test Index Homepage: " . uniqid());
		// $this->render("AppBundle::custom_layout.html.twig");
		// replace this example code with whatever you need
		return $this->render('default/index.html.twig', array(
			'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
		));
	}
}
