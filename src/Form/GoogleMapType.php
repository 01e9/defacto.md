<?php

namespace App\Form;

use App\Validator\Map;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints;

class GoogleMapType extends AbstractType
{
    const FLOAT_REGEX = '/^[-+]?[0-9]*\.?[0-9]*$/';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lat', TextType::class, [
                'label' => 'label.lat',
                'attr' => ['readonly' => true],
                'constraints' => [new Constraints\Regex(['pattern' => self::FLOAT_REGEX])],
            ])
            ->add('lng', TextType::class, [
                'label' => 'label.lng',
                'attr' => ['readonly' => true],
                'constraints' => [new Constraints\Regex(['pattern' => self::FLOAT_REGEX])],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }

    public function getBlockPrefix()
    {
        return 'google_map';
    }
}
