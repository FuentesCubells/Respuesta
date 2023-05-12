<?php

// src/Form/UploadFormType.php
namespace App\Form;


use App\Entity\Folder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Repository\FolderRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;

class UploadFormType extends AbstractType
{
    private $folderRepository;

    public function __construct(FolderRepository $folderRepository)
    {
        $this->folderRepository = $folderRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['user'];
        
        $builder->add('files', FileType::class, [
            'label' => 'Files to upload',
            'mapped' => false,
            'required' => true,
            'multiple' => true,
            'constraints' => [
                new All([
                    new Image([
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Please upload a valid JPG or PNG image.',
                    ]),
                ]),
            ],
        ]);
        
        $builder->add('directory', EntityType::class, [
            'class' => Folder::class,
            'label' => 'Target directory',
            'choice_label' => 'getFolderName',
            'placeholder' => 'Choose a directory',
            'constraints' => [
                new NotBlank(),
            ],
            'choices' => $this->folderRepository->findBy(['user' => $user])
        ]);

        $builder->add('submit', SubmitType::class, [
            'label' => 'Upload Files',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('user');
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
