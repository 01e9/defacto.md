<?php

namespace App\Form\Filter;

use App\Consts;
use App\Filter\MandateFilter;
use App\Repository\CompetenceCategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MandateFilterType extends AbstractType
{
    private CompetenceCategoryRepository $categoryRepository;

    public function __construct(CompetenceCategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(MandateFilter::QUERY_CATEGORY, ChoiceType::class, [
                'multiple' => false,
                'label' => 'label.category',
                'placeholder' => 'placeholder.all_categories',
                'required' => false,
                'choices' => $this->categoryRepository->getParentChoices(),
                'choice_value' => 'slug',
            ])
            ->add(MandateFilter::QUERY_FROM_DATE, DateType::class, [
                'label' => 'label.from_date',
                'placeholder' => 'label.from_date',
                'widget' => 'single_text',
                'format' => Consts::DATE_FORMAT_INTL,
                'html5' => false,
                'required' => false,
                'attr' => [
                    'readonly' => true,
                ],
            ])
            ->add(MandateFilter::QUERY_TO_DATE, DateType::class, [
                'label' => 'label.to_date',
                'placeholder' => 'label.to_date',
                'widget' => 'single_text',
                'format' => Consts::DATE_FORMAT_INTL,
                'html5' => false,
                'required' => false,
                'attr' => [
                    'readonly' => true,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'method' => "GET",
            'csrf_protection' => false,
            'allow_extra_fields' => true,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
