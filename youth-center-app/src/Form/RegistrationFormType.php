<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
    ->add('email')
    ->add('plainPassword', PasswordType::class, [
        'mapped' => false,
        'required' => true,
        'label' => 'Password',
        'attr' => ['autocomplete' => 'new-password'],
    ])
    ->add('roles', ChoiceType::class, [
        'choices' => [
            'Simple User' => 'ROLE_USER',
            'Technician' => 'ROLE_TECHNICIEN',
            'Center Manager' => 'ROLE_CENTER_MANAGER',
        ],
        'expanded' => false,
        'multiple' => true,
        'label' => 'Role',
    ])
      ->add('agreeTerms', CheckboxType::class, [
        'mapped' => false,
        'constraints' => [
            new IsTrue([
                'message' => 'You must agree to our terms.',
            ]),
        ],
    ]);
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
