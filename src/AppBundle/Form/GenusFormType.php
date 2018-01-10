<?php

namespace AppBundle\Form;

use AppBundle\Entity\SubFamily;
use AppBundle\Entity\User;
use AppBundle\Repository\SubFamilyRepository;
use AppBundle\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GenusFormType extends AbstractType
{
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view['funFact']->vars['help'] = 'For example, Leatherback sea turtles can travel more than 10,000 miles every year!';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            // because the 2nd param is set to null, Symfony guesses the entityType and the class option... sweet!
            //->add('subFamily', null, [
            //    'placeholder' => 'Choose something from the list...',
            //])
            ->add('subFamily', EntityType::class, [
                'placeholder'   => 'Choose something from the list...',
                'class'         => SubFamily::class,
                'query_builder' => function (SubFamilyRepository $repo) {
                    return $repo->createAlphabeticalQueryBuilder();
                },
            ])
            ->add('speciesCount')
            ->add('funFact')
            ->add('isPublished', ChoiceType::class, [
                'choices' => [
                    'Yea'  => true,
                    'Nein' => false,
                ],
            ])
            ->add('firstDiscoveredAt', DateType::class, [
                'widget' => 'single_text',
                'attr'   => [
                    'class' => 'js-datepicker',
                ],
                'html5'  => false,
            ])
            ->add('genusScientists', EntityType::class, [
                'class'    => User::class,
                'multiple' => true,
                'expanded' => true,
                'choice_label' => 'email',
                'query_builder' => function(UserRepository $repository) {
                    return $repository->createIsScientistQueryBuilder();
                }
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Genus',
        ]);
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_genus_form_type';
    }
}
