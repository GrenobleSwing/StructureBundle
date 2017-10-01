<?php

namespace GS\StructureBundle\Form\Type;

use GS\StructureBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('email', EmailType::class, array(
                    'disabled' => true,
                    'label' => 'Email',
                ))
                ->add('plainPassword', RepeatedType::class, array(
                    'type' => PasswordType::class,
                    'first_options'  => array('label' => 'Password'),
                    'second_options' => array('label' => 'Repeat Password'),
                ))
                ->add('roles', ChoiceType::class, array(
                    'label' => 'Roles',
                    'multiple' => true,
                    'choices' => array(
                        'Admin' => 'ROLE_ADMIN',
                        'Secretaire' => 'ROLE_SECRETARY',
                        'Tresorier' => 'ROLE_TREASURER',
                        'Prof' => 'ROLE_TOPIC_MANAGER',
                        'Organisateur' => 'ROLE_ORGANIZER',
                    ),
                    'attr' => array(
                        'class' => 'js-select-multiple',
                    ),
                ))
                ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
}
