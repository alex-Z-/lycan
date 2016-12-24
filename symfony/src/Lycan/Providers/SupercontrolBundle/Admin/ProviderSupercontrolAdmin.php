<?php

namespace Lycan\Providers\SupercontrolBundle\Admin;

use Lycan\Providers\CoreBundle\Admin\ProviderAdmin;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Admin\AdminInterface;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Lycan\Providers\CoreBundle\Form\Type\BrandsType;

class ProviderSupercontrolAdmin extends ProviderAdmin
{
	
	const  ACCESS_ROLE_FOR_USERFIELD ="ROLE_SUPERADMIN";
	
	
	public function getNewInstance()
	{
		$instance = parent::getNewInstance();
		$instance->setBaseUrl("http://api.supercontrol.co.uk/api/endpoint/v1/");
		$instance->setShouldPull(true);
		return $instance;
	}
	
	
	public function generateObjectUrl($name, $object, array $parameters = array(), $absolute = false)
	{
		
		// THIS DOESNT DO ANYTHING. NO IDEA HOW TO GENERATE OBJECT URL FOR THAT...
		if ('edit_owner' == $name) {
			
			return $this->getRouteGenerator()->generate('admin_sonata_user_user_edit', [
				'id' => $this->getUrlsafeIdentifier($object),
			], $absolute );
		}
		$parameters['id'] = $this->getUrlsafeIdentifier($object);
		return $this->generateUrl($name, $parameters, $absolute);
	}
	
	protected function configureFormFields(FormMapper $formMapper)
	{
		
		
		$user = $this->getConfigurationPool()->getContainer()->get('security.context')->getToken()->getUser();
		$accessToUserFields = ( $this->isGranted(  SELF::ACCESS_ROLE_FOR_USERFIELD ) && $this->getSubject()->getId() === null);
		$em = $this->modelManager->getEntityManager('AppBundle:Brand');
		
		// $builder = $formMapper->getFormBuilder()->getFormFactory()->createBuilder(BrandsType::class);
		
		if($this->getSubject()->getId() && $this->getSubject()->getOwner() ){
			$user =   $this->getSubject()->getOwner();
		}
		
		// WE ABSOLUTELY WANT THE EXISTING BRAND TO ALSO SHOW...
		$brandsQuery =  $query = $em->createQueryBuilder("b")
			->select("b")
			->from("AppBundle:Brand", "b")
			->leftjoin("b.members", "m")
			->where("b.owner = :owner or m.member = :owner")
			->setParameter("owner", $user->getId() );
		$choices = [];
		foreach($brandsQuery->getQuery()->getResult() as $brand){
			$choices[(string) $brand->getId()] = (string) $brand;
		}
		
		
		$formMapper
			->tab('Credentials')
			->with('Supercontrol Credentials', array('class' => 'col-md-7'))->end()
			->with('Channel Configuration', array('class' => 'col-md-5'))->end()
			->with('Provider Supports Features', array('class' => 'col-md-5'))->end()
			->end();
		
		
		
		$formMapper
			->tab('Credentials')
			->with('Supercontrol Credentials')
			->add('nickname', 'text')
				->add('baseUrl', 'url')
				->add('secret', 'text', [ 'label' => 'API Key' ])
				->add('client', 'text', [ 'label' => 'Client ID' ])
				->add('siteId', 'text', [ 'label' => 'Site ID', "required" => false ])
				
			->end()
			->with('Channel Configuration')
				->add('shouldPull', 'checkbox', ['required' => false, 'label' => 'Shall we pull rentals from this provider?'])
				->add('passOnCredentials', 'checkbox', ['required' => false, 'label' => 'Can Lycan share API credentials to downstream channels.'])
				->end()
				->with('Provider Supports Features')
				->add('supportsRealTimePricing', 'checkbox', ['required' => false, 'label' => 'Supports Realtime Pricing'])
				->end()
			->end();
		
		// We don't want to let properties be transfered until we understand more of the implications.
		if ($this->isGranted(  SELF::ACCESS_ROLE_FOR_USERFIELD ) ) {
			$formMapper
				->tab('Credentials')
				->with('Owner', array('class' => 'col-md-5'))
				->add('owner', 'sonata_type_model', array(
					'required' => false,
					'expanded' => false,
					'btn_add' => 'Create new user',
					'multiple' => false,
				))
				->end()
				->end();
		}
		
		$formMapper
			->tab('Credentials')
			->with("Auto Map to Brand" , [
				'class' => 'col-md-5',
				'box_class' => 'box box-primary',
				'description' => "When properties are pulled from the external provider, we can automatically add the listings to any brand that you  own or are a member of." .
								 "<br /><p><b>Working with other managers?</b><br />Brands which have been shared with you will also show up here.</p>"])
			->add('autoMappedToBrands', BrandsType::class , array(
					'choices' => $choices ,
					'required' => false,
					'expanded' => !empty($choices),
					'multiple' => true
				
				)
			)
			->end();
		
		
		
	}
	
	protected function configureDatagridFilters(DatagridMapper $datagridMapper)
	{
		$datagridMapper->add('baseUrl')
			->add('client');

	}
	
	protected function configureListFields(ListMapper $listMapper)
	{
		$listMapper
			->add('isValidCredentials', null, ['label' => 'Valid'])
			->addIdentifier('nickname', null, array(
				'route' => array(
					'name' => 'edit'
				)
			))
			->add('baseUrl')
			->add('client', null, array('label' => 'Site ID'))
			->add('owner')
			->add('propertiesCount')
			->add('lastPullCompletedAt')
			->add('_action', 'actions', array(
				'actions' => array(
					'edit' => array(),
					'delete' => array(),
					'pull' => array(
						'template' => 'CoreBundle:CRUD:list__action_pull.html.twig'
					)
				)
			));
		
		
	}
	
	public function createQuery($context = 'list')
	{
		$query = parent::createQuery($context);
		
		if ( !$this->isGranted("ROLE_SUPERADMIN") ){
			$owner = $this->getConfigurationPool()->getContainer()->get('security.context')->getToken()->getUser();
			$query->andWhere('o.owner = :owner')
			->setParameter('owner', $owner);
		}
		return $query;
	}
	
	public function prePersist($property)
	{
		if($property->getOwner() === null ){
			$owner = $this->getConfigurationPool()->getContainer()->get('security.context')->getToken()->getUser();
			$property->setOwner( $owner );
		}
		
	}
	
	
}