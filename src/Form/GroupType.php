<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Group;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('shortname')
            ->add('code')
            ->add('school')
            // ->add('users')         
        ;
        $builder->add('users', EntityType::class, array(
            'class' => User::class,
            'choice_label' => 'name',
            'multiple' => true,
            // 'expanded' => true, // render check-boxes
            'attr' => [
                'size' => 50,
            ],
        ));    
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Group::class,
        ]);
    }
}
