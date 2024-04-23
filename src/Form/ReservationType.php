<?php

namespace App\Form;

use App\Entity\Forfait;
use App\Entity\Reservation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Symfony\Component\Clock\now;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
//            ->add('number',HiddenType::class)
//            ->add('beginDate',HiddenType::class)
//            ->add('endDate',HiddenType::class)
//            ->add('userr',HiddenType::class)
            ->add('forfait', EntityType::class, [
                'class' => Forfait::class,
                'choice_label' => 'name',
            ])
            ->add('quantity',IntegerType::class,['data' => 1])
            ->add('renewable',CheckboxType::class,['label'=>'renouvelable'])
            ->add('submit', SubmitType::class,['label'=>'Réserver'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
