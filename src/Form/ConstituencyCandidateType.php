<?php

namespace App\Form;

use App\Consts;
use App\Entity\Candidate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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
            ->add('registrationDate', DateType::class, [
                'label' => 'label.registration_date',
                'widget' => 'single_text',
                'format' => Consts::DATE_FORMAT_INTL,
                'required' => false,
            ])
            ->add('registrationLink', TextType::class, [
                'label' => 'label.registration_link',
                'required' => false,
            ])
            ->add('registrationNote', TextType::class, [
                'label' => 'label.registration_note',
                'required' => false,
            ])
            ->add('electoralPlatform', TextareaType::class, [
                'label' => 'label.electoral_platform',
                'required' => false,
                'attr' => ['class' => 'wysiwyg'],
            ])
            ->add('electoralPlatformLink', TextType::class, [
                'label' => 'label.electoral_platform_link',
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
