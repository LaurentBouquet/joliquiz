<?php

namespace App\Form;

use App\Entity\Group;
use App\Form\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class GroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('code')
            ->add('school')
            ->add('users')         
        ;
        // $builder->add('users', CollectionType::class, array(
        //     'entry_type' => UserType::class,
        //     'entry_options' => array(
        //         'attr' => array('rows' => '7'),
        //     ),
        // ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Group::class,
        ]);
    }
}
