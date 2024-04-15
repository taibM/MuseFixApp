<?php

namespace App\Form;

use App\Entity\Commande;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('orderDate', DateTimeType::class, [
                'disabled' => true, // lecture seule avec disabled
                'data' => (new \DateTime('now'))->setTimezone(new \DateTimeZone('Africa/Tunis')), // Définit la valeur par défaut à la date et l'heure actuelles en UTC+1 (Tunisie)
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'En cours' => 'enCours',
                    'Livré' => 'livree',
                    'Annulé' => 'annule',

                ],
            ])
            ->add('modePaiement', ChoiceType::class, [
                'choices' => [
                    'Carte' => 'carte',
                    'Liquide' => 'liquide',
                    'PayPal' => 'paypal',
                ],
            ])
            ->add('adresseLivraison')
            ->add('fraisLivraison')
            ->add('total')
            ->add('userID', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
