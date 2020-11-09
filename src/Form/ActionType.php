<?php

namespace App\Form;

use App\Consts;
use App\Entity\PromiseAction;
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
        $is_edit = !!count($options['powers']);

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
                'required' => false,
                'attr' => ['class' => 'wysiwyg'],
            ])
            ->add('occurredTime', DateType::class, [
                'label' => 'label.occurred_time',
                'widget' => 'single_text',
                'format' => Consts::DATE_FORMAT_INTL,
                'html5' => false,
            ])
            ->add('mandate', ChoiceType::class, [
                'label' => 'label.mandate',
                'placeholder' => 'placeholder.choose_option',
                'choices' => $options['mandates'],
                'choice_value' => 'id',
                'disabled' => $is_edit,
            ])
            ->add('published', CheckboxType::class, [
                'label' => 'label.published',
                'required' => false,
            ])
            ->add('promiseUpdates', CollectionType::class, [
                'label' => 'label.promise_updates',
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'entry_type' => PromiseUpdateType::class,
                'entry_options' => [
                    'actions' => $options['actions'],
                    'promises' => $options['promises'],
                    'statuses' => $options['statuses'],
                ],
            ])
            ->add('usedPowers', ChoiceType::class, [
                'multiple' => true,
                'expanded' => true,
                'label' => 'label.used_powers',
                'choices' => $options['powers'],
                'choice_value' => 'id',
            ])
            ->add('sources', CollectionType::class, [
                'label' => 'label.sources',
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'entry_type' => ActionSourceType::class,
                'entry_options' => [
                    'actions' => $options['actions'],
                ],
            ])
        ;

        $builder->get('usedPowers')
            ->addModelTransformer(new CallbackTransformer(
                function ($powers) {
                    return ($powers instanceof Collection) ? $powers->toArray() : $powers;
                },
                function ($powers) {
                    return $powers;
                }
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PromiseAction::class,
            'mandates' => [],
            'actions' => [],
            'promises' => [],
            'statuses' => [],
            'powers' => [],
        ]);
    }
}
