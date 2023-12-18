<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Program;
use App\Entity\Actor;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ProgramType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('synopsis')
            ->add('posterFile', VichFileType::class, [
                'required'      => false,
                'allow_delete'  => true, 
                'download_uri' => true,
                ])
            ->add('country')
            ->add('year')
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name'
                ])
            ->add('actors', EntityType::class, [
                'class' => Actor::class,
                'by_reference' => false,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Program::class,
        ]);
    }
}
