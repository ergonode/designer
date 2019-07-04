<?php

/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See license.txt for license details.
 */

declare(strict_types = 1);

namespace Ergonode\Designer\Application\Form\Type;

use Ergonode\Designer\Application\Model\Form\Type\TemplateElementTypeModel;
use Ergonode\Designer\Application\Resolver\TemplateElementFormTypeResolver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 */
class TemplateElementType extends AbstractType implements EventSubscriberInterface
{
    /**
     * @var TemplateElementFormTypeResolver
     */
    private $resolver;

    /**
     * @param TemplateElementFormTypeResolver $resolver
     */
    public function __construct(TemplateElementFormTypeResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'type',
                TextType::class
            )
            ->add(
                'position',
                PositionFormType::class
            )
            ->add(
                'size',
                SizeFormType::class
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TemplateElementTypeModel::class,
            'translation_domain' => 'designer',
            'allow_extra_fields' => true,
        ]);
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function onPreSubmit(FormEvent $event)
    {
        $data = $event->getData();

        $class = $this->resolver->resolve($data['type']);

        $event->getForm()->add(
            'properties',
            $class
        );
    }
}
