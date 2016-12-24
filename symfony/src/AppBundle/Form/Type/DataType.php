<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DataType extends AbstractType
{
	
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			
		));
	}
	public function getBlockPrefix()
	{
		return "data";
	}
	
	public function getParent()
	{
		return TextareaType::class;
	}
}