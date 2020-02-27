<?php
namespace IndyDevGuy\Bundle\WikiBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EasyMDEType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'elementid'=>null,
        ]);
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['elementid'] = $options['elementid'];
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'easy_mde_type';
    }

    public function getParent()
    {
        return TextareaType::class;
    }
}