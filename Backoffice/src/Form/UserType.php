<?php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, array('label' => false,
            'attr' => ['class' => 'btn btn-default pull-right', 'placeholder' => "Adresse email"],
        ))
            ->add('username', TextType::class, array('label' => false,
                'attr' => ['class' => 'btn btn-default pull-right', 'placeholder' => "Nom d'utilisateur"],
            )
            )
            ->add('firstname', TextType::class, array('label' => false,
            'attr' => ['class' => 'btn btn-default pull-right', 'placeholder' => "Prénom"],
        ))
            ->add('lastname', TextType::class, array('label' => false,
            'attr' => ['class' => 'btn btn-default pull-right', 'placeholder' => "Nom de famille"],
        ))
            ->add('is_active', ChoiceType::class, array(
                'label' => false,
                'choices' => array(
                    'Oui' => true,
                    'Non' => false,
                ),
                'attr' => ['class' => 'form-control']
            ))
            ->add('businessname', TextType::class, array('label' => false,
            'attr' => ['class' => 'btn btn-default pull-right', 'placeholder' => "Nom de l'entreprise"],
        ))
            ->add('job', TextType::class, array('label' => false,
            'attr' => ['class' => 'btn btn-default pull-right', 'placeholder' => "Titre de poste"],
        ))
            ->add('rules', ChoiceType::class, array(
                'label' => false,
                'choices' => array(
                    'Administrateur' => 'ROLE_ADMIN',
                    'Client' => 'ROLE_CLIENT',
                    'Associé' => 'ROLE_ASSOCIATE',
                    'Trading' => 'ROLE_TRADING',
                    'Technique' => 'ROLE_TECHNIQUE',
                ),
                'attr' => ['class' => 'form-control']
            ))
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options' => array('label' => false, 'attr' => ['class' => 'btn btn-default pull-right', 'placeholder' => "Mot de passe"]),
                'second_options' => array('label' => false, 'attr' => ['class' => 'btn btn-default pull-right', 'placeholder' => "Répéter le mot de passe"]),
                'label' => false,
                'attr' => ['class' => 'btn btn-default pull-right', 'placeholder' => "Nom d'utilisateur"],
            
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
}
