<?php

namespace Application\Sonata\UserBundle\Admin;

use Sonata\UserBundle\Admin\Entity\UserAdmin as BaseAdmin;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Admin\AdminInterface;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Show\ShowMapper;
class UserAdmin extends  BaseAdmin
{
	
	public function getNewInstance()
	{
		$instance = parent::getNewInstance();
		$instance->setUsername( uniqid());
		$instance->setPlainPassword( uniqid());
		
		if(  $this->getRequest()->get('pcode', null) ) {
			
			$admin     = $this->getConfigurationPool()
				->getAdminByAdminCode($this->getRequest()
					->get('pcode'));
			
			$parent = $admin->getNewInstance();
			$dummy =  uniqid($parent->getProviderName() . "_" );
			if(method_exists($parent, 'getProviderName')){
				$instance->setUsername( $dummy );
			}
			
			$instance->SetEmail( $dummy . '@example.com' );
		}
		
		return $instance;
	}
	
	
	/**
	 * {@inheritdoc}
	 */
	protected function configureListFields(ListMapper $listMapper)
	{
		$listMapper
			->addIdentifier('username')
			->add('email')
			->add('groups')
			->add('propertiesCount', null, ['label' => 'Rentals'])
			->add('enabled', null, array('editable' => true))
			->add('locked', null, array('editable' => true))
			->add('createdAt')
		;
		
		if ($this->isGranted('ROLE_ALLOWED_TO_SWITCH')) {
			$listMapper
				->add('impersonating', 'string', array('template' => 'SonataUserBundle:Admin:Field/impersonating.html.twig'))
			;
		}
	}
	
	
	
	protected function configureFormFields(FormMapper $formMapper){
		// parent::configureFormFields($formMapper);
		
		
		// define group zoning
        $formMapper
            ->tab('User')
				->with('General', array('class' => 'col-md-6'))->end()
			->end()
			->tab('Profile')
                ->with('Profile', array('class' => 'col-md-6'))->end()
                ->with('Social', array('class' => 'col-md-6'))->end()
            ->end()
            ->tab('Security')
                ->with('Status', array('class' => 'col-md-4'))->end()
                ->with('Groups', array('class' => 'col-md-4'))->end()
                ->with('Keys', array('class' => 'col-md-4'))->end()
                ->with('Roles', array('class' => 'col-md-12'))->end()
            ->end()
        ;

        $now = new \DateTime();
		$emailRequired = (!$this->getSubject() || is_null($this->getSubject()->getId()));
		
        $formMapper
            ->tab('User')
                ->with('General')
                    ->add('username')
                    ->add('email')
                    ->add('plainPassword', 'text', array(
                        'required' => $emailRequired,
                    ))
                ->end()
			->end()
			->tab('Profile')
                ->with('Profile')
                    ->add('dateOfBirth', 'sonata_type_date_picker', array(
                        'years' => range(1900, $now->format('Y')),
                        'dp_min_date' => '1-1-1900',
                        'dp_max_date' => $now->format('c'),
                        'required' => false,
                    ))
                    ->add('firstname', null, array('required' => false))
                    ->add('lastname', null, array('required' => false))
                    ->add('website', 'url', array('required' => false))
                    ->add('biography', 'text', array('required' => false))
                    ->add('gender', 'sonata_user_gender', array(
                        'required' => true,
                        'translation_domain' => $this->getTranslationDomain(),
                    ))
                    ->add('locale', 'locale', array('required' => false))
                    ->add('timezone', 'timezone', array('required' => false))
                    ->add('phone', null, array('required' => false))
                ->end()
                ->with('Social')
                    ->add('facebookUid', null, array('required' => false))
                    ->add('facebookName', null, array('required' => false))
                    ->add('twitterUid', null, array('required' => false))
                    ->add('twitterName', null, array('required' => false))
                    ->add('gplusUid', null, array('required' => false))
                    ->add('gplusName', null, array('required' => false))
                ->end()
            ->end()
            ->tab('Security')
                ->with('Status')
                    ->add('locked', null, array('required' => false))
                    ->add('expired', null, array('required' => false))
                    ->add('enabled', null, array('required' => false))
                    ->add('credentialsExpired', null, array('required' => false))
                ->end()
                ->with('Groups')
                    ->add('groups', 'sonata_type_model', array(
                        'required' => false,
                        'expanded' => true,
                        'multiple' => true,
                    ))
                ->end()
                ->with('Roles')
                    ->add('realRoles', 'sonata_security_roles', array(
                        'label' => 'form.label_roles',
                        'expanded' => true,
                        'multiple' => true,
                        'required' => false,
                    ))
                ->end()
                ->with('Keys')
                    ->add('token', null, array('required' => false))
                    ->add('twoStepVerificationCode', null, array('required' => false))
                ->end()
            ->end()
        ;
		
	
		$formMapper->tab("Notes")
		->with("Your Personal Notes")
			->add('notes', 'textarea',  array('required' => false))
			->end()
		->end();
		
		$formMapper
			->tab('User')
			->with('General')
			->add('username')
			->add('email', 'text', ['required' => false])
			->add('plainPassword', 'text', array(
				'required' => (!$this->getSubject() || is_null($this->getSubject()->getId())),
			))
			->end();
		
	}
	
	
	protected function configureSideMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
	{
		
		if (!$childAdmin && !in_array($action, array('edit'))) {
			return;
		}
		
		$admin = $this->isChild() ? $this->getaParent() : $this;
		
		// $id = $admin->getRequest()->get('id');
		$router = $this->getConfigurationPool()->getContainer()->get('router');
		
		
		if($admin->getSubject()->getId()) {
			
			$menu->addChild(
				$this->trans('View Properties', array(), 'SonataUserBundle'),
				array('uri' => $router->generate('admin_app_property_list',
					array(
						// 'id' => (string) $admin->getSubject()->getId(),
						'filter[owner][value]' => (string)  $admin->getSubject()->getId()
					)
				))
			);
		}
		
		
	}
}