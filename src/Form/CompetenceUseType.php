<?php

namespace App\Form;

use App\Consts;
use App\Entity\CompetenceUse;
use App\Entity\Mandate;
use App\Repository\CompetenceRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompetenceUseType extends AbstractType
{
    private CompetenceRepository $competenceRepository;

    public function __construct(CompetenceRepository $competenceRepository)
    {
        $this->competenceRepository = $competenceRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Mandate $mandate */
        $mandate = $options['mandate'];
        $competenceChoices = $mandate
            ? $this->competenceRepository->getAdminChoicesByTitle($mandate->getInstitutionTitle()->getTitle())
            : [];

        $builder
            ->add('mandate', ChoiceType::class, [
                'label' => 'label.mandate',
                'choices' => ['~' => $options['mandate']],
                'choice_value' => 'id',
            ])
            ->add('competence', ChoiceType::class, [
                'label' => 'label.competence',
                'placeholder' => 'placeholder.choose_option',
                'choices' => $competenceChoices,
                'choice_value' => 'id',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'label.description',
                'required' => false,
                'attr' => ['class' => 'wysiwyg'],
            ])
            ->add('sourceLink', TextType::class, [
                'label' => 'label.source_link',
                'required' => false,
            ])
            ->add('useDate', DateType::class, [
                'label' => 'label.use_date',
                'widget' => 'single_text',
                'format' => Consts::DATE_FORMAT_INTL,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => false,
            'data_class' => CompetenceUse::class,
        ]);
        $resolver->setRequired(['mandate']);
    }

    public function getBlockPrefix()
    {
        return 'competence_use';
    }
}
