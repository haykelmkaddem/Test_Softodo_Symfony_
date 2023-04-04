<?php

namespace App\Form;

use App\Data\SearchData;
use App\Entity\Project;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('filenameOrUrl', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Search by filename or URL',
                    'class' => 'form-control'
                ]
            ])
            // ->add('projectName', TextType::class, [
            //     'label' => false,
            //     'required' => false,
            //     'attr' => [
            //         'placeholder' => 'Search by project name',

            //         'class' => 'form-control'
            //     ]
            // ])

            ->add(
                'projectName',
                EntityType::class,
                [
                    'class' => Project::class,
                    'choice_label' => 'title',
                    'label' => false,
                    'required' => false,
                    'attr' => [
                        'class' => 'form-select',
                    ],
                    'placeholder' => 'Project Name',
                ]
            )
            ->add('projectStatus', ChoiceType::class, [
                'choices' => [
                    'Status Projet' => '',
                    'In Progress' => 'In Progress',
                    'Done' => 'Done',
                    'Blocked' => 'Blocked',
                ],
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-select'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
