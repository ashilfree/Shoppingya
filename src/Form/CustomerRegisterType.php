<?php

namespace App\Form;

use App\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class CustomerRegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullName', TextType::class, [
                'label' => false
            ])
            ->add('email', EmailType::class, [
                'label'=>false
            ])
            ->add('username', TextType::class, [
                'label'=>false
            ])
            ->add('phone', TextType::class, [
                'label'=>false
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 8
                    ]),
                    new Regex([
                        'pattern' => "/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/",
                        'message' => "Password must be seven characters long and contain at least one digit, one upper case letter, one lower case letter and one special character"
                    ])
                ],
                'invalid_message' => 'The password fields must match.',
                'required' => true,
                'first_options'  => ['label' => false],
                'second_options' => ['label' => false],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Customer::class
        ]);
    }

}
