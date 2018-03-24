<?php

namespace App\Form;

use App\Entity\Status;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StatusType extends AbstractType
{
    public static function getColors()
    {
        return ['red', 'orange', 'yellow', 'green', 'blue', 'violet', 'grey'];
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'label.name',
            ])
            ->add('namePlural', TextType::class, [
                'label' => 'label.name_plural',
            ])
            ->add('slug', TextType::class, [
                'label' => 'label.slug',
                'attr' => ['data-slug-from' => 'status[namePlural]'],
            ])
            ->add('effect', IntegerType::class, [
                'label' => 'label.effect',
                'scale' => 0,
            ])
            ->add('color', ChoiceType::class, [
                'label' => 'label.color',
                'placeholder' => 'placeholder.choose_option',
                'choices' => (function(){
                    $choices = [];
                    foreach (self::getColors() as $color) {
                        $choices['label.color_name.'. $color] = $color;
                    }
                    return $choices;
                })(),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Status::class,
        ]);
    }
}
