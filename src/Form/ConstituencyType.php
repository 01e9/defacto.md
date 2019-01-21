<?php

namespace App\Form;

use App\Entity\Constituency;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ConstituencyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'label.name',
            ])
            ->add('slug', TextType::class, [
                'label' => 'label.slug',
                'attr' => ['data-slug-from' => 'constituency[name]'],
            ])
            ->add('link', TextType::class, [
                'label' => 'label.constituency_link',
            ])
            ->add('problems', CollectionType::class, [
                'label' => 'label.problems',
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'entry_type' => ConstituencyProblemType::class,
                'entry_options' => [
                    'constituencies' => $options['constituencies'],
                    'elections' => $options['elections'],
                    'problems' => $options['problems'],
                ],
            ])
            ->add('candidates', CollectionType::class, [
                'label' => 'label.candidates',
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'entry_type' => ConstituencyCandidateType::class,
                'entry_options' => [
                    'constituencies' => $options['constituencies'],
                    'elections' => $options['elections'],
                    'politicians' => $options['politicians'],
                    'parties' => $options['parties'],
                ],
            ])
            ->add('candidateProblemOpinions', CollectionType::class, [
                'label' => 'label.candidate_problem_opinions',
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'entry_type' => CandidateProblemOpinionType::class,
                'entry_options' => [
                    'constituencies' => $options['constituencies'],
                    'politicians' => $options['politicians'],
                    'elections' => $options['elections'],
                    'problems' => $options['problems'],
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Constituency::class,
            'constituencies' => [],
            'elections' => [],
            'problems' => [],
            'politicians' => [],
            'parties' => [],
        ]);
    }
}
