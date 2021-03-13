<?php

namespace App\Form;

use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', hiddenType::class)
            ->add('shippingFirstName', TextType::class, [
                'label'=>false
            ])
            ->add('shippingLastName', TextType::class, [
                'label'=>false
            ])
            ->add('shippingAddress', TextType::class, [
                'label'=>false,
                'attr' => [
                    'id' => 'address'
                ]
            ])
            ->add('shippingCity', TextType::class, [
                'label'=>false
            ])
            ->add('shippingProvince', TextType::class, [
                'label'=>false
            ])
            ->add('shippingPostalCode', TextType::class, [
                'label'=>false
            ])
            ->add('shippingLat', HiddenType::class)
            ->add('shippingLng', HiddenType::class)
            ->add('shippingEmail', EmailType::class, [
                'label'=>false
            ])
            ->add('shippingPhone', TextType::class, [
                'label'=>false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
