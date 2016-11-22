<?php

namespace Lycan\Providers\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class BrandsType extends AbstractType
{
	
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'choices' => array(
				
			)
		));
	}
	
	public function getParent()
	{
		return ChoiceType::class;
	}
}