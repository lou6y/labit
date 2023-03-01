<?php

namespace App\Form;

use App\Entity\Service;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Nom', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('Description', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('DescriptionLong', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('imageFile', VichImageType::class, [

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
        ]);
    }
}
