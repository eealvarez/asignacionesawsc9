<?php

namespace INFUNISA\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('firstName')
            ->add('lastName')
            ->add('email', 'email') //tipo de campo: email
            ->add('password', 'password')
            ->add('role', 'choice', array('choices' => array('ROLE_ADMIN' => 'Administrator', 'ROLE_USER' => 'User'), 'placeholder' => 'Select a role'))
            ->add('isActive', 'checkbox')
           // ->add('createdAt') //Estos campos no necesitamos definirlos porque los vamos a definir más adelante, ya que estos campos no los vamos a renderizar en el formulario
           // ->add('updatedAt') //Queremos que estos dos campos se generen de forma automática
            ->add('save', 'submit', array('label' => 'Save user'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'INFUNISA\UserBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'user';
    }
}
