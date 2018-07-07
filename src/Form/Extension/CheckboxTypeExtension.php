<?php

namespace App\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;

class CheckboxTypeExtension extends AbstractTypeExtension
{
    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return CheckboxType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        // makes it legal for CheckboxType fields to have an label_property option
        $resolver->setDefined(array('text_property'));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (isset($options['text_property'])) {
            // this will be whatever class/entity is bound to your form (e.g. Answer)
            $parentData = $form->getParent()->getData();

            $text = null;
            if (null !== $parentData) {
                $accessor = PropertyAccess::createPropertyAccessor();
                $text = $accessor->getValue($parentData, $options['text_property']);
            }

            // sets an "text" variable that will be available when rendering this field
            $view->vars['label'] = $text;
        }
    }

}
