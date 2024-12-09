<?php

namespace App\Form;

use App\Entity\Trainer;
use App\Entity\WorkoutProgram;
use App\Entity\WorkoutSession;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkoutSessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
            $builder
                ->add('start_time', DateTimeType::class, [
                    'widget' => 'single_text',
                    'input' => 'datetime',
                    'required' => true,
                ])
                ->add('end_time', DateTimeType::class, [
                    'widget' => 'single_text',
                    'input' => 'datetime',
                    'required' => true,
                ])
                ->add('program', EntityType::class, [
                    'class' => WorkoutProgram::class,
                    'choice_label' => 'id',
                    'required' => true,
                ])
                ->add('trainer', EntityType::class, [
                    'class' => Trainer::class,
                    'choice_label' => 'id',
                    'required' => true,
                ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WorkoutSession::class,
        ]);
    }
}
