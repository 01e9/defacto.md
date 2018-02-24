<?php

namespace App\Form;

use App\Entity\Mandate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MandateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('beginDate', DateType::class, [
                'label' => 'label.begin_date',
                'widget' => 'single_text',
            ])
            ->add('endDate', DateType::class, [
                'label' => 'label.end_date',
                'widget' => 'single_text',
            ])
            ->add('politician', ChoiceType::class, [
                'label' => 'label.politician',
                'placeholder' => 'placeholder.choose_option',
                'choices' => $options['politicians'],
                'choice_value' => 'id',
            ])
            ->add('institutionTitle', ChoiceType::class, [
                'label' => 'label.institution_title',
                'placeholder' => 'placeholder.choose_option',
                'choices' => $options['institution_titles'],
                'choice_value' => 'id',
            ])
            ->add('votesCount', NumberType::class, [
                'label' => 'label.votes_count',
                'scale' => 0,
            ])
            ->add('votesPercent', NumberType::class, [
                'label' => 'label.votes_percent',
                'scale' => 2,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Mandate::class,
            'politicians' => [],
            'institution_titles' => [],
        ]);
    }
}
