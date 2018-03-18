<?php

namespace App\Form;

use App\Entity\Action;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'label.name',
            ])
            ->add('slug', TextType::class, [
                'label' => 'label.slug',
                'attr' => ['data-slug-from' => 'action[name]'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'label.description',
                'attr' => ['class' => 'wysiwyg'],
            ])
            ->add('occurredTime', DateType::class, [
                'label' => 'label.occurred_time',
                'widget' => 'single_text',
            ])
            ->add('mandate', ChoiceType::class, [
                'label' => 'label.mandate',
                'placeholder' => 'placeholder.choose_option',
                'choices' => $options['mandates'],
                'choice_value' => 'id',
            ])
            ->add('published', CheckboxType::class, [
                'label' => 'label.published',
                'required' => false,
            ])
            ->add('statusUpdates', CollectionType::class, [
                'label' => 'label.status_updates',
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'entry_type' => StatusUpdateType::class,
                'entry_options' => [
                    'actions' => $options['actions'],
                    'promises' => $options['promises'],
                    'statuses' => $options['statuses'],
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Action::class,
            'mandates' => [],
            'actions' => [],
            'promises' => [],
            'statuses' => [],
        ]);
    }
}
