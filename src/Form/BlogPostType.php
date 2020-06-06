<?php

namespace App\Form;

use App\Consts;
use App\Entity\BlogPost;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\File;

class BlogPostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'label.title',
            ])
            ->add('slug', TextType::class, [
                'label' => 'label.slug',
                'attr' => ['data-slug-from' => 'blog_post[title]'],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'label.content',
                'attr' => ['class' => 'wysiwyg'],
                'required' => false,
            ])
            ->add('publishTime', DateType::class, [
                'label' => 'label.published_date',
                'widget' => 'single_text',
                'format' => Consts::DATE_FORMAT_INTL,
                'required' => false,
                'help' => 'text.leave_blank_to_not_publish',
            ])
            ->add('imageUpload', FileType::class, [
                'mapped' => false,
                'label' => 'label.image',
                'required' => false,
                'help' => 'text.recommended_image_ratio_16_9',
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => ["image/jpeg", "image/png", "image/gif"],
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BlogPost::class,
        ]);
    }
}
