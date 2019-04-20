<?php

namespace App\Form;

use App\Data\Filter\PromisesFilterData;
use App\Repository\ElectionRepository;
use App\Repository\PoliticianRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PromisesFilterType extends AbstractType
{
    private $politicianRepository;
    private $electionRepository;

    public function __construct(
        PoliticianRepository $politicianRepository,
        ElectionRepository $electionRepository
    )
    {
        $this->politicianRepository = $politicianRepository;
        $this->electionRepository = $electionRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', TextType::class, [
                'label' => 'label.code',
                'required' => false,
            ])
            ->add('politician', ChoiceType::class, [
                'label' => 'label.politician',
                'placeholder' => '',
                'required' => false,
                'choices' => $this->politicianRepository->getAdminChoices(),
                'choice_value' => 'id',
            ])
            ->add('election', ChoiceType::class, [
                'label' => 'label.election',
                'placeholder' => '',
                'required' => false,
                'choices' => $this->electionRepository->getAdminChoices(),
                'choice_value' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'method' => "GET",
            'csrf_protection' => false,
            'data_class' => PromisesFilterData::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
