<?php
namespace AppBundle\Block;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use SymfonyComponentHttpFoundationResponse;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Helper\CSVTypes;

class UpsertCSVService extends AbstractBlockService
{

    private $container = null;

    public function __construct($name, $templating, $container=null)
    {
        parent::__construct($name, $templating);
        $this->container = $container;
    }

    public function getName()
    {
        return 'Import CSV';
    }

    public function getDefaultSettings()
    {
        return array();
    }

   

   
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        // merge settings
        $settings = array_merge($this->getDefaultSettings(), $blockContext->getSettings());

        $curBlock='AppBundle:Block:block_import_csv.html.twig';
        if (!$this->container->get('security.context')->isGranted("ROLE_SUPER_ADMIN")) {
            $curBlock='AppBundle:Block:block_import_empty.html.twig';
        }
		$user = $this->container->get('security.context')->getToken()->getUser();
		$em = $this->container->get('doctrine')->getManager();
		
		
		// Just show all Brands.
		$brandsQuery =  $query = $em->createQueryBuilder("b")
			->select("b")
			->from("AppBundle:Brand", "b")
			->leftjoin("b.members", "m");
			
		$choices = [  false => "None - Do not automap"];
		foreach($brandsQuery->getQuery()->getResult() as $brand){
			$choices[(string) $brand->getId()] = (string) $brand;
		}
	
		$defaultData = array('message' => 'Type your message here');
		$options = [
			'choices'  => $choices
		];
		$form = $this->container->get('form.factory')->createBuilder('form')
			->add('brands', ChoiceType::class, $options)
			->getForm();
	
		return $this->renderResponse($curBlock, array(
            'block'     => $blockContext->getBlock(),
			'form' => $form->createView(),
            'allTypes'  => CSVTypes::getTypesAndIds(),
            'settings'  => $settings
            ), $response);
    }
}