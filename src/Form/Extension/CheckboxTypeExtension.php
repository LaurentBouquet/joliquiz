<?php

namespace App\Form\Extension;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

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
    
    public static function getExtendedTypes(): iterable
    {
        return [CheckboxType::class];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        // makes it legal for CheckboxType fields to have an label_property option
        $resolver->setDefined(array('text_property'));
        $resolver->setDefined(array('correct_given_property'));
        $resolver->setDefined(array('correct_property'));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (isset($options['text_property'])) {
            // this will be whatever class/entity is bound to your form (e.g. Answer)
            $parentData = $form->getParent()->getData();

            $label = null;
            $value = false;
            $labelAttr = array();

            if (null !== $parentData) {
                $accessor = PropertyAccess::createPropertyAccessor();

                $label = $accessor->getValue($parentData, $options['text_property']);

                if (isset($options['correct_given_property'])) {
                    $value = $accessor->getValue($parentData, $options['correct_given_property']);
                }

                if (isset($options['correct_property'])) {
                    $view->vars['disabled'] = true;
                    $correct_property = $accessor->getValue($parentData, $options['correct_property']);
                    if ($correct_property) {
                        $labelAttr = array('class' => 'alert-success');
                    }
                    else {
                        //$labelAttr = array('class' => 'alert-danger');
                    }
                }
            }

            // sets an "text" variable that will be available when rendering this field
            $view->vars['label'] = $label;
            $view->vars['value'] = $value;
            $view->vars['label_attr'] = $labelAttr;
        }
    }

}
