<?php

namespace App\Form;

use App\Entity\Campus;
use App\Repository\CampusRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManagerInterface;

class SearchType extends AbstractType
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('search', TextType::class, [
                'required' => false,
                'label' => 'Recherche par nom :',
                'attr' => [
                    'placeholder' => 'Rechercher une sortie...',
                ],
            ])
            ->add('campus', EntityType::class, [
                'required' => false,
                'label' => 'Campus :',
                'placeholder' => 'Sélectionnez un campus',
                'class' => Campus::class,
                'query_builder' => function (CampusRepository $repository) {
                    return $repository->createQueryBuilder('campus')
                        ->orderBy('campus.nom', 'ASC');
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'entityManager' => null,
        ]);
    }

    private function getCampusChoices(): array
    {
        $campuses = $this->entityManager->getRepository(Campus::class)->findAll();

        $choices = [];
        foreach ($campuses as $campus) {
            $choices[$campus->getNom()] = $campus->getId();
        }

        return $choices;
    }
}