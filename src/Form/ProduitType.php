<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Nom', TextType::class, [
                'attr' => [
            'class' => 'form-control'
                 ]
            ])
            ->add('Prix', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ] )
            ->add('Stock', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('Description', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('Type',ChoiceType::class, [
                'choices'  => [
                    'Used' => 0,
                    'New' => 1,
                ],])

            ->add('imageFile', VichImageType::class, [

            ])

            ->add('CategoryId', EntityType::class, [
                'class' => Category::class,'choice_label'=>'Name'
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
