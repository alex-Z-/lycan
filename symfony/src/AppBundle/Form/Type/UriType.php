<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UriType extends AbstractType
{
	
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			
		));
	}
	public function getBlockPrefix()
	{
		return "uri";
	}
	
	public function getParent()
	{
		return UrlType::class;
	}
}