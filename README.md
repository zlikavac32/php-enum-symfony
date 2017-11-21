# PHP Enum Symfony

[![Build Status](https://travis-ci.org/zlikavac32/php-enum-symfony.svg?branch=master)](https://travis-ci.org/zlikavac32/php-enum-symfony) 

Symfony support for [zlikavac32/php-enum](https://github.com/zlikavac32/php-enum).

## Table of contents

1. [Installation](#installation)
1. [Usage](#usage)
    1. [Form](#form)
    1. [Validator](#validator)
1. [Limitations](#usage)

## Installation

Recommended installation is through Composer.

```
composer require zlikavac32/php-enum-symfony
```

## Usage

Assumption is that there exists a valid enum `\YesNoEnum`.

### Form

Form type for enum is provided as `\Zlikavac32\SymfonyEnum\Form\Type\EnumType`. There is one required options `enum_class` which must contain enum class FQN.

Internally this extends [\Symfony\Component\Form\Extension\Core\Type\ChoiceType](https://symfony.com/doc/current/reference/forms/types/choice.html) and populates choices from the defining `enum_class`.

If any of the `choices` and/or `choice_loader` options is/are passed, an `\LogicException` will be thrown. Since these fields are overridden internally, passing them from the outside could cloud code's original purpose. Any other option provided by the `\Symfony\Component\Form\Extension\Core\Type\ChoiceType` can be used.

```php
use \Zlikavac32\SymfonyEnum\Form\Type\EnumType;

class FormModel
{
    public $answer;
    
    // ...
}

$formModel = new FormModel();

$form = $this->createFormBuilder($formModel)
    ->add('answer', EnumType::class, [
        'enum_class' => \YesNoEnum::class
    ])
    // ...
    ->getForm();
```

### Validator

Two constraints are provided, `\Zlikavac32\SymfonyEnum\Validator\Constraints\ValidEnumElement` and `\Zlikavac32\SymfonyEnum\Validator\Constraints\ValidEnumElementName`. Internally, they use `\Symfony\Component\Validator\Constraints\Choice`.

Required constraint argument is `enumClass` which must contain enum class FQN.

If any of the `choices`, `callback` and/or `strict` options is/are passed, an `\LogicException` will be thrown. Since these fields are overridden internally, passing them from the outside could cloud code's original purpose. Any other option provided by the `\Symfony\Component\Validator\Constraints\Choice` can be used.

- `ValidEnumElement` - accepted values are `null` and any valid enum element from the defined enum class FQN
- `ValidEnumElementName` - accepted values are `null` and any valid enum element name from the defined enum class FQN

Example for annotation use:

```php
/**
 * @ValidEnumElement(enumClass="\YesNoEnum")
 */
```

## Limitations

Due to [doctrine/common issue #794](https://github.com/doctrine/common/issues/794) with checks for aliased namespaces, validation of form enum element within an array will throw exception in following cases:

- on `Windows` validation does not work at all (due to the anonymous classes)
- on `Linux` - short enum definition (one that uses `eval()`) does not work so the workaround is to manually instantiate elements
- on `OSX` - have to check but I'd assume same as `Linux`

For more details on what's wrong and why, feel free to check related issue.
