<?php

namespace App\Form;

use App\Entity\ConstituencyProblem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConstituencyProblemType extends AbstractType
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
            ->add('problem', ChoiceType::class, [
                'label' => 'label.problem',
                'placeholder' => 'placeholder.choose_option',
                'choices' => $options['problems'],
                'choice_value' => 'id',
            ])
            ->add('respondents', NumberType::class, [
                'label' => 'label.respondents',
                'required' => false,
            ])
            ->add('percentage', NumberType::class, [
                'label' => 'label.percentage',
                'required' => false,
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'label.problem_type',
                'placeholder' => 'placeholder.no_type',
                'choices' => [
                    "label.problem_types.local" => "local",
                    "label.problem_types.national" => "national",
                ],
                'required' => false,
            ])
            ->add('questionnaireEmbedLink', UrlType::class, [
                'label' => 'label.questionnaire_embed_link',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => false,
            'data_class' => ConstituencyProblem::class,
            'constituencies' => [],
            'elections' => [],
            'problems' => [],
        ]);
    }

    public function getBlockPrefix()
    {
        return 'constituency_problem';
    }
}
