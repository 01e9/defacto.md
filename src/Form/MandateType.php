<?php

namespace App\Form;

use App\Consts;
use App\Entity\Mandate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
                'format' => Consts::DATE_FORMAT_INTL,
            ])
            ->add('ceasingDate', DateType::class, [
                'label' => 'label.ceasing_date',
                'widget' => 'single_text',
                'format' => Consts::DATE_FORMAT_INTL,
                'required' => false,
                'help' => 'text.fill_to_mark_as_ceased',
            ])
            ->add('endDate', DateType::class, [
                'label' => 'label.end_date',
                'widget' => 'single_text',
                'format' => Consts::DATE_FORMAT_INTL,
            ])
            ->add('election', ChoiceType::class, [
                'label' => 'label.election',
                'placeholder' => 'placeholder.choose_option',
                'choices' => $options['elections'],
                'choice_value' => 'id',
            ])
            ->add('constituency', ChoiceType::class, [
                'label' => 'label.constituency',
                'placeholder' => 'placeholder.no_constituency',
                'choices' => $options['constituencies'],
                'choice_value' => 'id',
                'required' => false,
            ])
            ->add('politician', ChoiceType::class, [
                'label' => 'label.politician',
                'placeholder' => 'placeholder.no_mandate_politician',
                'choices' => $options['politicians'],
                'choice_value' => 'id',
                'required' => false,
            ])
            ->add('institutionTitle', ChoiceType::class, [
                'label' => 'label.institution_title',
                'placeholder' => 'placeholder.choose_option',
                'choices' => $options['institution_titles'],
                'choice_value' => 'id',
            ])
            ->add('votesCount', NumberType::class, [
                'label' => 'label.votes_count',
            ])
            ->add('votesPercent', NumberType::class, [
                'label' => 'label.votes_percent',
            ])
            ->add('decisionLink', TextType::class, [
                'label' => 'label.decision_link',
                'required' => false,
            ])
            ->add('ceasingLink', TextType::class, [
                'label' => 'label.ceasing_link',
                'required' => false,
            ])
            ->add('ceasingReason', TextType::class, [
                'label' => 'label.ceasing_reason',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Mandate::class,
            'politicians' => [],
            'institution_titles' => [],
            'elections' => [],
            'constituencies' => [],
        ]);
    }
}
