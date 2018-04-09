<?php

namespace App\Form;

use App\Entity\Promise;
use Doctrine\Common\Collections\Collection;
use KMS\FroalaEditorBundle\Form\Type\FroalaEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PromiseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'label.title',
            ])
            ->add('slug', TextType::class, [
                'label' => 'label.slug',
                'attr' => ['data-slug-from' => 'promise[name]'],
            ])
            ->add('description', FroalaEditorType::class, [
                'label' => 'label.description',
                'required' => false,
            ])
            ->add('madeTime', DateType::class, [
                'label' => 'label.made_date',
                'widget' => 'single_text',
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'label.status',
                'placeholder' => 'placeholder.no_status',
                'choices' => $options['statuses'],
                'choice_value' => 'id',
                'required' => false,
            ])
            ->add('mandate', ChoiceType::class, [
                'label' => 'label.mandate',
                'placeholder' => 'placeholder.choose_option',
                'choices' => $options['mandates'],
                'choice_value' => 'id',
            ])
            ->add('categories', ChoiceType::class, [
                'multiple' => true,
                'expanded' => true,
                'label' => 'label.categories',
                'choices' => $options['categories'],
                'choice_value' => 'id',
            ])
            ->add('published', CheckboxType::class, [
                'label' => 'label.published',
                'required' => false,
            ])
            ->add('sourceName', TextType::class, [
                'label' => 'label.source_name',
                'required' => false,
            ])
            ->add('sourceLink', TextType::class, [
                'label' => 'label.source_link',
                'required' => false,
            ])
        ;

        $builder->get('categories')
            ->addModelTransformer(new CallbackTransformer(
                function ($categories) {
                    return ($categories instanceof Collection) ? $categories->toArray() : $categories;
                },
                function ($categories) {
                    return $categories;
                }
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Promise::class,
            'statuses' => [],
            'mandates' => [],
            'categories' => [],
        ]);
    }
}
