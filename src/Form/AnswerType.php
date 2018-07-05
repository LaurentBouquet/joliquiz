<?php

namespace App\Form;

use App\Entity\Answer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnswerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        switch ($options['form_type']) {
            case 'student':
                $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                    $answerText = ' ';
                    $answer = $event->getData();
                    $form = $event->getForm();
                    if ($answer) {
                        $answerText = $answer->getText();
                    }
                    $form->add('correct_given', CheckboxType::class, array(
                        'label' => $answerText,
                    ));
                });
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
