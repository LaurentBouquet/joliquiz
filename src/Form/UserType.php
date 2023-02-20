<?php

namespace App\Form;

use App\Entity\Group;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UserType extends AbstractType
{
    private $checker;
    private $translator;

    public function __construct(AuthorizationCheckerInterface $checker, TranslatorInterface $translator)
    {
        $this->checker = $checker;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {        

        switch ($options['form_type']) {
            case 'login':
                // $builder->add('username', TextType::class);
                $builder->add('plainPassword', PasswordType::class, array('label' => $this->translator->trans('Password')));
                break;

            case 'new':
                $builder->add('username', TextType::class);
                $builder->add('email', EmailType::class);
                $builder->add('firstname', TextType::class);
                $builder->add('lastname', TextType::class);
                $builder->add(
                    'plainPassword',
                    RepeatedType::class,
                    [
                        'type' => PasswordType::class,
                        'first_options'  => array('label' => $this->translator->trans('Password')),
                        'second_options' => array('label' => $this->translator->trans('Repeat Password'))
                    ]
                );
                if ($this->checker->isGranted('ROLE_SUPER_ADMIN')) {
                    $builder->add('roles', ChoiceType::class, array(
                        'multiple' => true,
                        'expanded' => true, // render check-boxes
                        'choices' => array(
                            'Teacher' => 'ROLE_TEACHER',
                            'Admin' => 'ROLE_ADMIN',
                            'Super admin' => 'ROLE_SUPER_ADMIN',
                        ),
                    ));
                }
                $builder->add('isActive', CheckboxType::class, array(
                    'required' => false,
                    'label' => $this->translator->trans('Account activated'),
                ));
                $builder->add('groups', EntityType::class, array(
                    'class' => Group::class,
                    // 'query_builder' => function (GroupRepository $er) {
                    //     return $er->createQueryBuilder('c')->andWhere('c.language = :language')->setParameter('language', $this->param->get('locale'))->orderBy('c.shortname', 'ASC');
                    //  },
                    'choice_label' => 'name',
                    'multiple' => true,
                    // 'expanded' => true, // render check-boxes   
                ));
                $builder->add('toReceiveMyResultByEmail', CheckboxType::class, [
                    'label' => $this->translator->trans('To receive result by email'),
                    'required' => false,
                ]);
                break;

            case 'update':
                $builder->add('username', TextType::class);
                $builder->add('email', EmailType::class);
                $builder->add('firstname', TextType::class);
                $builder->add('lastname', TextType::class);
                if ($this->checker->isGranted('ROLE_SUPER_ADMIN')) {
                    $builder->add('roles', ChoiceType::class, array(
                        'multiple' => true,
                        'expanded' => true, // render check-boxes
                        'choices' => array(
                            'Teacher' => 'ROLE_TEACHER',
                            'Admin' => 'ROLE_ADMIN',
                            'Super admin' => 'ROLE_SUPER_ADMIN',
                        ),
                    ));
                }
                $builder->add('groups', EntityType::class, array(
                    'class' => Group::class,
                    // 'query_builder' => function (GroupRepository $er) {
                    //     return $er->createQueryBuilder('c')->andWhere('c.language = :language')->setParameter('language', $this->param->get('locale'))->orderBy('c.shortname', 'ASC');
                    //  },
                    'choice_label' => 'name',
                    'multiple' => true,
                ));
                $builder->add('isActive', CheckboxType::class, array(
                    'required' => false,
                    'label' => $this->translator->trans('Account activated'),
                ));
                $builder->add('toReceiveMyResultByEmail', CheckboxType::class, [
                    'label' => $this->translator->trans('To receive result by email'),
                    'required' => false,
                ]);
                // if ($this->checker->isGranted('ROLE_SUPER_ADMIN')) {
                //     $builder->add(
                //         'plainPassword',
                //         RepeatedType::class,
                //         [
                //             'type' => PasswordType::class,
                //             'first_options'  => array('label' => $this->translator->trans('Password')),
                //             'second_options' => array('label' => $this->translator->trans('Repeat Password'))
                //         ]
                //     );
                // }
                break;
                case 'profile':
                    $builder->add('lastname', TextType::class, array(
                        'attr' => array(
                            'readonly' => false,
                        ),
                    ));                
                    $builder->add('firstname', TextType::class, array(
                        'attr' => array(
                            'readonly' => false,
                        ),
                    ));
                    $builder->add('username', TextType::class, array(
                        'attr' => array(
                            'readonly' => true,
                            'class' => 'bg-light',
                        ),
                    ));  
                    $builder->add('email', TextType::class, array(
                        'attr' => array(
                            'readonly' => true,
                            'class' => 'bg-light',
                        ),
                    ));                
                    $builder->add('toReceiveMyResultByEmail', CheckboxType::class, [
                        'label' => $this->translator->trans('To receive my result by email'),
                        'required' => false,
                    ]);
                    break;    
            case 'password':
                $builder->add('email', TextType::class, array(
                    'attr' => array(
                        'readonly' => true,
                    ),
                ));                 
                $builder->add(
                    'plainPassword',
                    RepeatedType::class,
                    [
                        'type' => PasswordType::class,
                        'first_options'  => array('label' => $this->translator->trans('Password')),
                        'second_options' => array('label' => $this->translator->trans('Repeat Password'))
                    ]
                );
                break;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'form_type' => 'register',
            'login_type' => '',
        ]);
    }
}
