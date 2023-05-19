<?php

namespace App\Form;

use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la sortie'
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'label'=> 'Date et heure de la sortie',
              'date_widget' => 'single_text'
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'label'=>'Date limite d inscription',
                'html5'=>true,
                'widget' => 'single_text',
            ])
            ->add('duree', DateIntervalType::class, [
                'label' => 'Durée',
                'with_years' => false,
                'with_months' => false,
                'with_days' => false,
                'with_hours' => false,
                'with_minutes' => true,
                'with_seconds' => false,
                'input' => 'dateinterval',
                'widget' => 'choice',
                'hours' => range(0, 5),
                'minutes' => array_combine(range(10, 180), range(10, 180))
            ])
               /* 'choices'=> array_combine(range(10,180), range(10, 180)),
                'choice_label' => function($value){
                return $value . ' minutes';
                }*/
            ->add('nbInscriptionsMax', ChoiceType::class, [
                'label'=> 'Nombre de places',
                'choices'=> array_combine(range(1, 50), range(1, 50)),
            ])
            ->add('infosSortie', TextareaType::class, [
                'label'=> 'Description et infos',
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
