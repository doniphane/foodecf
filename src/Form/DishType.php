<?php

namespace App\Form;

use App\Entity\Dish;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class DishType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du plat',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le nom du plat est obligatoire'
                    ])
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix',
                'currency' => 'EUR',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le prix est obligatoire'
                    ]),
                    new Positive([
                        'message' => 'Le prix doit être positif'
                    ])
                ]
            ])
            ->add('category', ChoiceType::class, [
                'label' => 'Catégorie',
                'choices' => [
                    'Entrée' => 'entrees',
                    'Plat' => 'plats',
                    'Dessert' => 'desserts'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'La catégorie est obligatoire'
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Dish::class,
        ]);
    }
}
