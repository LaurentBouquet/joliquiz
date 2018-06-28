<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraints\IsTrue;

class UserType extends AbstractType
{
    private $checker;

    public function __construct(AuthorizationCheckerInterface $checker)
    {
        $this->checker = $checker;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', TextType::class);

        switch ($options['form_type']) {
            case 'register':
                $builder->add('email', EmailType::class);
                $builder->add(
                    'plainPassword',
                    RepeatedType::class,
                    [
                    'type' => PasswordType::class,
                    'first_options'  => array('label' => 'Password'),
                    'second_options' => array('label' => 'Repeat Password')
                    ]
                );
                $builder->add('termsAccepted', CheckboxType::class, array(
                    'mapped' => false,
                    'constraints' => new IsTrue(),
                  )
                );
                break;

            case 'login':
                $builder->add('plainPassword', PasswordType::class, array('label' => 'Password'));
                break;

            // register + update
            case 'new':
                $builder->add('email', EmailType::class);
                $builder->add(
                    'plainPassword',
                    RepeatedType::class,
                    [
                    'type' => PasswordType::class,
                    'first_options'  => array('label' => 'Password'),
                    'second_options' => array('label' => 'Repeat Password')
                    ]
                );
                if ($this->checker->isGranted('ROLE_SUPER_ADMIN')) {
                    $builder->add('roles', ChoiceType::class, array(
                        'multiple' => true,
                        'expanded' => true, // render check-boxes
                        'choices' => array(
                            'Admin' => 'ROLE_ADMIN',
                            'Super admin' => 'ROLE_SUPER_ADMIN',
                        ),
                    ));
                }
                $builder->add('isActive', CheckboxType::class, array(
                    'required' => false,
                ));
                break;

            case 'update':
                $builder->add('email', EmailType::class);
                if ($this->checker->isGranted('ROLE_SUPER_ADMIN')) {
                    $builder->add('roles', ChoiceType::class, array(
                        'multiple' => true,
                        'expanded' => true, // render check-boxes
                        'choices' => array(
                            'Admin' => 'ROLE_ADMIN',
                            'Super admin' => 'ROLE_SUPER_ADMIN',
                        ),
                    ));
                }
                $builder->add('isActive', CheckboxType::class, array(
                    'required' => false,
                ));
                break;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'form_type' => 'register',
        ]);
    }
}
