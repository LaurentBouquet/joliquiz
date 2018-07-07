<?php

namespace App\Form;

use App\Entity\Answer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnswerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        switch ($options['form_type']) {
            case 'student':
                $builder->add('workout_correct_given', CheckboxType::class, array(
                    'label' => false,
                    'text_property' => 'text',
                    'required' => false,
                ));
                break;
            case 'teacher':
                $builder->add('text', null, array(
                    'label' => false,
                ));
                $builder->add('correct');
                break;
        }

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Answer::class,
            'form_type' => 'student',
        ]);
    }
}
