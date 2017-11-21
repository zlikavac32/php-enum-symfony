<?php

declare(strict_types=1);

namespace Zlikavac32\SymfonyEnum\Form\Type;

use LogicException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zlikavac32\Enum\Enum;

class EnumType extends ChoiceType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->assertThatOverriddenOptionsAreNotSet($options);

        /* @var Enum|string $enumClass */
        $enumClass = $options['enum_class'];

        $this->assertThatEnumClassIsValid($enumClass);

        $options = $this->populateOptionsWithDefaults($options);

        parent::buildForm(
            $builder,
            [
                'choices'       => $this->buildChoicesForEnumClass($enumClass),
                'choice_loader' => null,
            ] + $options
        );
    }

    private function populateOptionsWithDefaults(array $options): array
    {
        $elementNameClosure = function (?Enum $enum): string {
            if (null === $enum) {
                return '';
            }

            return $enum->name();
        };

        foreach (['choice_label', 'choice_value'] as $optionKey) {
            if (isset($options[$optionKey])) {
                continue;
            }

            $options[$optionKey] = $elementNameClosure;
        }

        return $options;
    }

    private function buildChoicesForEnumClass(string $enumClass): array
    {
        $choices = [];

        /* @var Enum $enumClass Just for IDE auto-complete support */
        foreach ($enumClass::values() as $element) {
            $choices[$element->name()] = $element;
        }

        return $choices;
    }

    private function assertThatEnumClassIsValid(string $enumClass): void
    {
        if (false === is_subclass_of($enumClass, Enum::class)) {
            throw new LogicException(sprintf('%s does not have %s as it\'s parent', $enumClass, Enum::class));
        }
    }

    private function assertThatOverriddenOptionsAreNotSet(array $options): void
    {
        $optionsToCheckFor = [
            'choices',
            'choice_loader',
        ];

        foreach ($optionsToCheckFor as $optionName) {
            if (empty($options[$optionName])) {
                continue;
            }

            throw new LogicException(
                sprintf('Option %s is overridden by the type so don\'t pass your own value', $optionName)
            );
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(['enum_class' => null])
            ->setAllowedTypes('enum_class', ['string']);
    }
}
