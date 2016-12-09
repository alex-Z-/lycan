<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Listing;
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
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
	
		
		$consumer = new PullListingConsumer(
		  $this->container->get('app.logger.jobs'),
			$this->container->get('doctrine')->getEntityManager()
		);
		$consumer->setContainer($this->container);


		
		$i = ["id"=>53777, "provider" => 5, "jobsInBatch" => 33, "jobIndex" => 1 ];
		$msg = new AMQPMessage(serialize($i));
		
		$consumer->execute($msg);
		

		die("Test Index Homepage: " . uniqid());
		// $this->render("AppBundle::custom_layout.html.twig");
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
        ));
    }
	
	/**
	 * @Route("/push", name="homepage")
	 */
	public function pushAction(Request $request)
	{
		
		
		$consumer = new PushListingConsumer(
			$this->container->get('app.logger.jobs'),
			$this->container->get('doctrine')->getEntityManager()
		);
		$consumer->setContainer($this->container);
		
		$i = ["id"=>'1475e475-d8b7-4bd6-8819-b594b4de2d28', "provider" => 2, "batch" => 'a5e9d4f9-1948-4b24-8a19-0c4e41b77cb4', "jobsInBatch" => 33, "jobIndex" => 1 ];
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
