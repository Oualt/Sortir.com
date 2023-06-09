<?php

namespace App\Form;

use App\Entity\Sortie;
use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Ville;
use App\Repository\CampusRepository;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la sortie'
            ])

            // ajout du champ dateHeureDebut dans le formulaire, contenant la date et l'heure de la sortie
            ->add('dateHeureDebut', DateTimeType::class, [
                'label' => 'Date et heure de la sortie',
                // 'html5' => true,
                'widget' => 'single_text',

            ])

            // ajout du champ dateLimiteInscription dans le formulaire, contenant la date limite d'inscription à la sortie

            ->add('dateLimiteInscription', DateTimeType::class, [
                'label' => 'Date limite d inscription',
                'html5' => true,
                'widget' => 'single_text',

            ])

            // ajout du champ duree dans le formulaire, contenant la durée de la sortie
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
                'minutes' => array_combine(range(10, 180), range(10, 180)),
                'attr' =>['class' => 'duree']
            ])
            /* 'choices'=> array_combine(range(10,180), range(10, 180)),
                'choice_label' => function($value){
                return $value . ' minutes';
                }*/

            // ajout du champ nbInscriptionsMax dans le formulaire, contenant le nombre de places maximum pour la sortie
            ->add('nbInscriptionsMax', ChoiceType::class, [
                'label' => 'Nombre de places',
                'choices' => array_combine(range(1, 50), range(1, 50)),
            ])

            // ajout du champ infosSortie dans le formulaire, contenant la description et les infos de la sortie
            ->add('infosSortie', TextareaType::class, [
                'label' => 'Description et infos',
            ])

            // ajout du champ campus dans le formulaire, contenant le campus de la sortie
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'query_builder' => function (CampusRepository $repository) {
                    return $repository->createQueryBuilder('campus')
                        ->orderBy('campus.nom', 'ASC');}
            ])

            // ajout du champ ville dans le formulaire, contenant la ville de la sortie
            ->add('ville', EntityType::class, [
                'mapped' => false,
                'class' => Ville::class,
                'choice_label' => 'nom',
                'required' => false,
                'placeholder'=> 'Ville',
                'label'=> 'Ville',
                'query_builder' => function (VilleRepository $villeRepository) {
                    return $villeRepository->createQueryBuilder('ville')
                        ->orderBy('ville.nom', 'ASC');}
            ])

            // ajout du champ lieu dans le formulaire, contenant le lieu de la sortie
            ->add('lieu',EntityType::class, [
                'mapped' => false,
                'class' => Lieu::class,
                'choice_label' => 'nom',
                'required' => false,
                'placeholder'=> 'Lieu',
                'query_builder' => function (LieuRepository $lieuRepository) {
                    return $lieuRepository->createQueryBuilder('lieu')
                        ->orderBy('lieu.nom', 'ASC');}
            ]);


           /* $formModifier = function (FormInterface $form, Ville $ville ){
                $lieu = $ville->getLieux();

                $form->add('lieu', EntityType::class, [
                    'class'=> Lieu::class,
                    'choices'=> $lieu,
                    'choice_label'=> 'nom',
                    'placeholder'=> 'Lieu (Choisir une ville)',
                    'label'=> 'Lieu'
                ]);
            };

            $builder->get('ville')->addEventListener(
                FormEvents::POST_SUBMIT,
                function (formEvent $event) use ($formModifier){
                    $ville = $event->getForm()->getData();
                    $formModifier($event->getForm()->getParent(), $ville);
                }
            );*/
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
