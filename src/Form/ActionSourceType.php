<?php

namespace App\Form;

use App\Entity\ActionSource;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActionSourceType extends AbstractType
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
            ->add('name', TextType::class, [
                'label' => 'label.source_name',
                'required' => true,
            ])
            ->add('link', TextType::class, [
                'label' => 'label.source_link',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => false,
            'data_class' => ActionSource::class,
            'actions' => [],
        ]);
    }

    public function getBlockPrefix()
    {
        return 'action_source';
    }
}
