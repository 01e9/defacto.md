<?php

namespace App\Form;

use App\Entity\StatusUpdate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StatusUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('action', ChoiceType::class, [
                'label' => 'label.action',
                'placeholder' => count($options['actions']) > 1 ? 'placeholder.choose_option' : null,
                'choices' => $options['actions'],
                'choice_value' => 'id',
            ])
            ->add('promise', ChoiceType::class, [
                'label' => 'label.promise',
                'placeholder' => 'placeholder.choose_option',
                'choices' => $options['promises'],
                'choice_value' => 'id',
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'label.status',
                'placeholder' => 'placeholder.choose_option',
                'choices' => $options['statuses'],
                'choice_value' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => false,
            'data_class' => StatusUpdate::class,
            'actions' => [],
            'promises' => [],
            'statuses' => [],
        ]);
    }

    public function getBlockPrefix()
    {
        return 'status_update';
    }
}
