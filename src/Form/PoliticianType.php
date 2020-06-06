<?php

namespace App\Form;

use App\Consts;
use App\Entity\Politician;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PoliticianType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'label.first_name',
            ])
            ->add('lastName', TextType::class, [
                'label' => 'label.last_name',
            ])
            ->add('slug', TextType::class, [
                'label' => 'label.slug',
                'attr' => ['data-slug-from' => 'politician[firstName],politician[lastName]'],
            ])
            ->add('photoUpload', FileType::class, [
                'mapped' => false,
                'label' => 'label.photo',
                'required' => false,
                'help' => 'text.recommended_image_ratio_1_1',
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => ["image/jpeg", "image/png", "image/gif"],
                    ])
                ],
            ])
            ->add('birthDate', DateType::class, [
                'label' => 'label.birth_date',
                'widget' => 'single_text',
                'format' => Consts::DATE_FORMAT_INTL,
                'required' => false,
            ])
            ->add('studies', TextareaType::class, [
                'label' => 'label.studies',
                'required' => false,
                'attr' => ['class' => 'wysiwyg'],
            ])
            ->add('profession', TextType::class, [
                'label' => 'label.profession',
                'required' => false,
            ])
            ->add('website', TextType::class, [
                'label' => 'label.website',
                'required' => false,
            ])
            ->add('facebook', TextType::class, [
                'label' => 'label.facebook',
                'required' => false,
            ])
            ->add('email', TextType::class, [
                'label' => 'label.email',
                'required' => false,
            ])
            ->add('phone', TextType::class, [
                'label' => 'label.phone',
                'required' => false,
            ])
            ->add('previousTitles', TextareaType::class, [
                'label' => 'label.previous_titles',
                'attr' => ['class' => 'wysiwyg'],
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Politician::class,
        ]);
    }
}
