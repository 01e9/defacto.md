<?php

namespace App\Form;

use App\Entity\PromiseSource;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PromiseSourceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('promise', ChoiceType::class, [
                'label' => 'label.promise',
                'placeholder' => count($options['promises']) > 1 ? 'placeholder.choose_option' : null,
                'choices' => $options['promises'],
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
            'data_class' => PromiseSource::class,
            'promises' => [],
        ]);
    }

    public function getBlockPrefix()
    {
        return 'promise_source';
    }
}
