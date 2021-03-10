<?php

namespace App\Form;

use App\Classes\Filter;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tags', EntityType::class, [
                "label" => false,
                "required" => false,
                "class" => Tag::class,
                "expanded" => true,
                "multiple" => true
            ])
            ->add('min', NumberType::class, [
                'label' =>false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Min Price'
                ]
            ])
            ->add('max', NumberType::class, [
                'label' =>false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Max Price'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Filter::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
