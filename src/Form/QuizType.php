<?php

namespace App\Form;

use App\Entity\Quiz;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class QuizType extends AbstractType
{
    private $translator;
    private $param;

    public function __construct(TranslatorInterface $translator, ParameterBagInterface $param)
    {
        $this->translator = $translator;
        $this->param = $param;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title');
        $builder->add('summary');
        $builder->add('number_of_questions');
        $builder->add('categories', EntityType::class, array(
                'class' => Category::class,
                'query_builder' => function (CategoryRepository $er) {
                    return $er->createQueryBuilder('c')->andWhere('c.language = :language')->setParameter('language', $this->param->get('locale'))->orderBy('c.shortname', 'ASC');
                 },
                'choice_label' => 'longname',
                'multiple' => true
            ));
        $builder->add('start_quiz_comment');
        $builder->add('show_result_question');
        $builder->add('result_quiz_comment');
        $builder->add('allow_anonymous_workout');
        $builder->add('active');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Quiz::class,
            'form_type' => 'student_questioning',
        ]);
    }
}
