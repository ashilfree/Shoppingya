<?php

namespace App\Form;

use App\Classes\Filter;
use App\Entity\Tag;
use App\Entity\Tog;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterArType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('togs', EntityType::class, [
                "label" => false,
                "required" => false,
                "class" => Tog::class,
                "expanded" => true,
                "multiple" => true
            ])
            ->add('min', NumberType::class, [
                'label' =>false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'سعر الحد الأدنى'
                ]
            ])
            ->add('max', NumberType::class, [
                'label' =>false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'السعر الأقصى'
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
