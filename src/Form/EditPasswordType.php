<?php

namespace App\Form;

use App\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class EditPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('old_password', PasswordType::class, [
                'mapped' => false,
                'label' => false
            ])
            ->add('new_password', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
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
                'first_options' => ['label' => false],
                'second_options' => ['label' => false],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Update'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
