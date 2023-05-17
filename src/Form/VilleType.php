<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VilleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', EntityType::class, [
                'label'=> 'Ville',
                'class' => Ville::class,
                'choice_label' => 'nom',
                'multiple' => false
            ])
            ->add('codePostal',EntityType::class, [
                'label'=> 'Code Postal',
                'class'=> Ville::class,
                'choice_label'=>'code_postal',

                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ville::class,
        ]);
    }
}
