<?php

namespace App\Form;

use App\Entity\Candidate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConstituencyCandidateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('constituency', ChoiceType::class, [
                'label' => 'label.constituency',
                'placeholder' => count($options['constituencies']) > 1 ? 'placeholder.choose_option' : null,
                'choices' => $options['constituencies'],
                'choice_value' => 'id',
            ])
            ->add('election', ChoiceType::class, [
                'label' => 'label.election',
                'placeholder' => 'placeholder.choose_option',
                'choices' => $options['elections'],
                'choice_value' => 'id',
            ])
            ->add('politician', ChoiceType::class, [
                'label' => 'label.politician',
                'placeholder' => 'placeholder.choose_option',
                'choices' => $options['politicians'],
                'choice_value' => 'id',
            ])
            ->add('party', ChoiceType::class, [
                'label' => 'label.party',
                'placeholder' => 'placeholder.no_party',
                'choices' => $options['parties'],
                'choice_value' => 'id',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => false,
            'data_class' => Candidate::class,
            'constituencies' => [],
            'elections' => [],
            'politicians' => [],
            'parties' => [],
        ]);
    }

    public function getBlockPrefix()
    {
        return 'constituency_candidate';
    }
}
