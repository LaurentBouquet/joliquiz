<?php

namespace App\Form;

use App\Entity\Question;
use App\Entity\Answer;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        switch ($options['form_type']) {
            case 'student_questioning':
            case 'student_marking':
                $builder->add('text', TextType::class, array(
                    'label' => false,
                    'disabled' => true,
                ));
                $builder->add('answers', CollectionType::class, array(
                    'label' => false,
                    'entry_type' => AnswerType::class,
                    'entry_options' => array('label' => false, 'form_type' => $options['form_type']),
                ));
                break;
            case 'teacher':
                $builder->add('text');
                $builder->add('categories', EntityType::class, array(
                    'class' => Category::class,
                    'choice_label' => 'longname',
                    'multiple' => true
                ));
                $builder->add('answers', CollectionType::class, array(
                    'entry_type' => AnswerType::class,
                    'entry_options' => array(
                        'label' => false,
                        'form_type' => $options['form_type'],
                    ),
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                ));
                break;
        }

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
            'form_type' => 'student_questioning',
        ]);
    }
}
