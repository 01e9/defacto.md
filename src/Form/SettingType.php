<?php

namespace App\Form;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        switch ($options['type']) {
            case 'string':
                $builder->add('value', TextType::class, [
                    'label' => $options['label']
                ]);
                break;
            case 'App:InstitutionTitle':
                $builder->add('value', ChoiceType::class, [
                    'label' => $options['label'],
                    'placeholder' => 'placeholder.choose_option',
                    'choices' => $this->entityManager->getRepository($options['type'])->getAdminChoices(),
                ]);
                $builder->get('value')->addModelTransformer(new CallbackTransformer(
                    function ($value) { return $value; },
                    function ($value) { return $value->getId(); }
                ));
                break;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => '?',
            'type' => null,
        ]);
    }
}
