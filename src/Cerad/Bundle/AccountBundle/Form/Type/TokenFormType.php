<?php
namespace Cerad\Bundle\AccountBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TokenFormType extends AbstractType
{
    public function getName() { return 'cerad_account_token'; }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => true,
            'intention'       => 'authenticate', // Needs to match security.yml
        ));
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('token', 'text', array('label' => 'Security Token',  'attr' => array('size' => 30)))
        ;
    }
}

?>
