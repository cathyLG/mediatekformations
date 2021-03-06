<?php

namespace App\Form;

use App\Entity\Formation;
use App\Entity\Niveau;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormationType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
                ->add('title', null, [
                    'label' => 'Titre',
                    'required' => true
                ])
                ->add('publishedAt', null, [
                    'label' => 'Date de parution',
                    'widget' => 'single_text',
                    'required' => true
                ])
                ->add('description')
                ->add('miniature', null, [
                    'label' => 'Miniature (120 x 90)'
                ])
                ->add('picture')
                ->add('videoId')
                ->add('niveau', EntityType::class, [
                    'class' => Niveau::class,
                    'choice_label' => 'nom',
                    'required' => true
                ])
                ->add('submit', SubmitType::class, [
                    'label' => 'Enregistrer'
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }

}
