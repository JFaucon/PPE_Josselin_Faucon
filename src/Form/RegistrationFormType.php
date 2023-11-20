<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastName', TextType::class, ['label'=>'Nom'])
            ->add('firstName', TextType::class, ['label'=>'Prenom'])
            ->add('email', EmailType::class)
            ->add("agreeTerms", CheckboxType::class, [
                'label'=>"Condition d'utilisation",
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter pour vous inscrire',
                    ]),
                ],
            ])
            ->add('password', PasswordType::class, [
                'label'=>'Mot de passe',
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'attr' => ['autocomplete' => 'new-password']
            ])
            ->add('confirmPassword',PasswordType::class,[
                'mapped' => false,
                'label'=>'Confirmation du mots de passe'
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
