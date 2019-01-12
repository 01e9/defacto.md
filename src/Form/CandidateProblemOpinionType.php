<?php

namespace App\Form;

use App\Entity\CandidateProblemOpinion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CandidateProblemOpinionType extends AbstractType
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
            ->add('politician', ChoiceType::class, [
                'label' => 'label.politician',
                'placeholder' => 'placeholder.choose_option',
                'choices' => $options['politicians'],
                'choice_value' => 'id',
            ])
            ->add('election', ChoiceType::class, [
                'label' => 'label.election',
                'placeholder' => 'placeholder.choose_option',
                'choices' => $options['elections'],
                'choice_value' => 'id',
            ])
            ->add('problem', ChoiceType::class, [
                'label' => 'label.problem',
                'placeholder' => 'placeholder.choose_option',
                'choices' => $options['problems'],
                'choice_value' => 'id',
            ])
            ->add('opinion', TextareaType::class, [
                'label' => 'label.opinion',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => false,
            'data_class' => CandidateProblemOpinion::class,
            'politicians' => [],
            'elections' => [],
            'constituencies' => [],
            'problems' => [],
        ]);
    }

    public function getBlockPrefix()
    {
        return 'candidate_problem_opinion';
    }
}
