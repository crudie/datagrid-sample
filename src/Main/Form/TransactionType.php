<?php

namespace Main\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class TransactionType extends AbstractType
{
    /**
     * Build form
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sum', NumberType::class, [
                'required' => true,
                'constraints' => [new Assert\NotBlank(), new Assert\Type('numeric')],
                'label' => 'Amount'
            ])
            ->add('comment', TextareaType::class, [
                'required' => false,
                'label' => 'Comment'
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Main\Model\Transaction',
            'allow_extra_fields' => true,
        ));
    }
}