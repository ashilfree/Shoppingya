<?php

namespace App\Form;

use App\Classes\CartItem;
use App\Entity\Size;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('size', EntityType::class, [
            'placeholder' => 'Choose an option',
            'class' => Size::class,
            "label" => false,
            "required" => true,
            "expanded" => false,
            "multiple" => false
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
//        $resolver->setDefaults([
//            'data_class' => CartItem::class,
//        ]);
    }
}
