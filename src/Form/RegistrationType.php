<?php

namespace App\Form;

use App\Entity\User;
use DateTime;
use Doctrine\DBAL\Types\DateTimeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
public function buildForm(FormBuilderInterface $builder, array $options): void
{
$builder
->add('nom')
->add('prenom')
->add('email')
    ->add('passwd', RepeatedType::class, [
        'type' => PasswordType::class,
        'invalid_message' => 'The password fields must match.',
        'options' => ['attr' => ['class' => 'password-field']],
        'required' => true,
        'first_options' => ['label' => 'Password'],
        'second_options' => ['label' => 'Repeat Password'],
    ])
->add('adresse')
->add('tel')
     // Optionally, add the signupdate field but mark it as disabled and not mapped
        ->add('signupdate', null, [
            'disabled' => true,
            'mapped' => false,
        ]);


    $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
        $user = $event->getData();
        $user->setRole(1); // Set role to 1 (client)
    });
}

public function configureOptions(OptionsResolver $resolver): void
{
$resolver->setDefaults([
// configure data class
'data_class' => User::class,
]);
}
}
