<?php
namespace Cerad\Bundle\AccountBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Validator\Constraints as Assert;

use Cerad\Bundle\AccountBundle\Validator\Constraints\UsernameUnique;

class UsernameFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      // $builder->addModelTransformer(new VolunteerIDTransformer());
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'label'           => 'User Name (unique)',
            'attr'            => array('size' => 30),
//            'constraints'     => array(
//                new Assert\NotNull(array('message' => 'User Name is required')), 
//                new UsernameUnique(),
//            )
        ));
    }
    public function getParent() { return 'text'; }
    public function getName()   { return 'cerad_account_username'; }
}

?>
