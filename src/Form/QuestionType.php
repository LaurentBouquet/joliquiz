<?php

namespace App\Form;

use App\Entity\Answer;
use App\Entity\Category;
use App\Entity\Question;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class QuestionType extends AbstractType
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
        switch ($options['form_type']) {
            case 'student_questioning':
            case 'student_marking':
                $builder->add('text', TextareaType::class, array(
                    'label' => false,
                    'disabled' => true,
                    'attr' => array('rows' => '7'),
                ));
                $builder->add('answers', CollectionType::class, array(
                    'label' => false,
                    'entry_type' => AnswerType::class,
                    'entry_options' => array('label' => false, 'form_type' => $options['form_type']),
                ));
                break;
            case 'teacher':
                $builder->add('text');
                $builder->add('max_duration', IntegerType::class, array(
                    'label' => 'Question max duration (seconds)',
                ));
                $builder->add('categories', EntityType::class, array(
                    'class' => Category::class,
                    'query_builder' => function (CategoryRepository $er) {
                        return $er->createQueryBuilder('c')->andWhere('c.language = :language')->setParameter('language', $this->param->get('locale'))->orderBy('c.shortname', 'ASC');
                     },
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
