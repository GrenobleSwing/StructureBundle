<?php

namespace GS\StructureBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use GS\StructureBundle\Entity\Topic;
use GS\StructureBundle\Form\Type\ScheduleType;

class TopicType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('category', EntityType::class, array(
                    'label' => 'Categories',
                    'class' => 'GSStructureBundle:Category',
                    'choice_label' => 'name',
                    'position' => array('before' => 'requiredTopics'),
                ))
//                A ameliorer pour ne prendre en compte que ceux de l'annee
                ->add('requiredTopics', EntityType::class, array(
                    'label' => 'Pre-requis',
                    'class' => 'GSStructureBundle:Topic',
                    'choice_label' => 'title',
                    'multiple' => true,
                    'required' => false,
                    'attr' => array(
                        'class' => 'js-select-multiple',
                    ),
                ))
                ->add('title', TextType::class, array(
                    'label' => 'Titre',
                ))
                ->add('description', TextareaType::class, array(
                    'label' => 'Description',
                ))
                ->add('type', ChoiceType::class, array(
                    'label' => 'Type de cours',
                    'choices' => array(
                        'Solo' => 'solo',
                        'Couple' => 'couple',
                        'Adhesion' => 'adhesion',
                    ),
                ))
                ->add('allowSemester', ChoiceType::class, array(
                    'label' => "Autoriser l'inscription au semestre",
                    'choices' => array(
                        "Hériter de l'activité" => 'PARENT',
                        'Oui' => 'TRUE',
                        'Non' => 'FALSE',
                    ),
                ))
                ->add('autoValidation', ChoiceType::class, array(
                    'label' => 'Validation automatique des inscriptions a ce topic ?',
                    'choices' => array(
                        "Oui" => true,
                        "Non" => false
                    )
                ))
                ->add('schedules', CollectionType::class, array(
                    'label' => 'Planning',
                    'entry_type' => ScheduleType::class,
                    'by_reference' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'attr' => array(
                        'class' => 'js-collection',
                    ),
                ))
                ->add('owners', EntityType::class, array(
                    'label' => 'Admins',
                    'class' => 'GSStructureBundle:User',
                    'choice_label' => 'email',
                    'multiple' => true,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('u')
                                ->orderBy('u.username', 'ASC');
                    },
                    'required' => false,
                    'attr' => array(
                        'class' => 'js-select-multiple',
                    ),
                ))
                ->add('moderators', EntityType::class, array(
                    'label' => 'Moderateurs',
                    'class' => 'GSStructureBundle:User',
                    'choice_label' => 'email',
                    'multiple' => true,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('u')
                                ->orderBy('u.username', 'ASC');
                    },
                    'required' => false,
                    'attr' => array(
                        'class' => 'js-select-multiple',
                    ),
                ))
                ->add('submit', SubmitType::class)
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $topic = $event->getData();
            $form = $event->getForm();

            if (null !== $topic && null !== $topic->getActivity()) {
                $form->remove('category');
                $form->add('category', EntityType::class, array(
                    'label' => 'Categories',
                    'class' => 'GSStructureBundle:Category',
                    'choice_label' => 'name',
                    'position' => array('before' => 'requiredTopics'),
                    'choices' => $topic->getActivity()->getCategories(),
                ));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Topic::class,
        ));
    }

}
