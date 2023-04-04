<?php

namespace App\Form;

use App\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ProjectTypeEdit extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title',TextType::class, [
                'label' => 'Project Title',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Image (JPG file)',
                'attr' => [
                    'class' => 'form-control',
                ],
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png'
                        ],
                        'mimeTypesMessage' => 'Please upload a valid JPG image',
                    ])
                ],
            ])
            ->add('filenameOrUrl',TextType::class, [
                'label' => 'Project Title',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('number_of_tasks',TextType::class, [
                'label' => 'Project Title',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('description',TextType::class, [
                'label' => 'Project Title',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'In Progress' => 'In Progress',
                    'Done' => 'Done',
                    'Blocked' => 'Blocked',
                ],
                'label' => 'Project Status',
                'attr' => [
                    'class' => 'form-select',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
