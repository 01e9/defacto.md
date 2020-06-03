<?php

namespace App\Form;

use App\Entity\Competence;
use App\Repository\CompetenceCategoryRepository;
use App\Repository\TitleRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CompetenceType extends AbstractType
{
    private TitleRepository $titleRepository;
    private CompetenceCategoryRepository $competenceCategoryRepository;

    public function __construct(
        TitleRepository $titleRepository,
        CompetenceCategoryRepository $competenceCategoryRepository
    )
    {
        $this->titleRepository = $titleRepository;
        $this->competenceCategoryRepository = $competenceCategoryRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'label.name',
            ])
            ->add('code', TextType::class, [
                'label' => 'label.code',
            ])
            ->add('slug', TextType::class, [
                'label' => 'label.slug',
                'attr' => ['data-slug-from' => 'competence[code]'],
            ])
            ->add('points', NumberType::class, [
                'label' => 'label.points',
                'attr' => ['placeholder' => '0.0',],
            ])
            ->add('title', ChoiceType::class, [
                'label' => 'label.institution_title',
                'placeholder' => 'placeholder.choose_option',
                'multiple' => false,
                'choices' => $this->titleRepository->getAdminChoices(),
                'choice_value' => 'id',
            ])
            ->add('category', ChoiceType::class, [
                'label' => 'label.category',
                'placeholder' => 'placeholder.choose_option',
                'multiple' => false,
                'choices' => $this->competenceCategoryRepository->getAdminChoices(),
                'choice_value' => 'id',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Competence::class,
        ]);
    }
}
