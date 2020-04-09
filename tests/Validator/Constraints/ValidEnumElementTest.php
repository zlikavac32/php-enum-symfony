<?php

declare(strict_types=1);

namespace Zlikavac32\SymfonyEnum\Tests\Validator\Constraints;

use stdClass;
use Symfony\Component\Validator\Constraints\ChoiceValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;
use Zlikavac32\SymfonyEnum\Tests\Fixtures\YesNoEnum;
use Zlikavac32\SymfonyEnum\Validator\Constraints\ValidEnumElement;

class ValidEnumElementTest extends ConstraintValidatorTestCase
{

    protected function createValidator()
    {
        return new ChoiceValidator();
    }

    public function testNullIsValid()
    {
        $this->validator->validate(
            null,
            new ValidEnumElement(
                [
                    'enumClass' => YesNoEnum::class,
                ]
            )
        );

        $this->assertNoViolation();
    }

    public function testThatEnumClassOptionIsDefaultOption()
    {
        $this->validator->validate(
            YesNoEnum::YES(),
            new ValidEnumElement(YesNoEnum::class)
        );

        $this->assertNoViolation();
    }

    public function testThatInvalidInstanceBuildsViolation()
    {
        $this->validator->validate(
            new stdClass(),
            new ValidEnumElement(
                [
                    'enumClass' => YesNoEnum::class,
                ]
            )
        );


        $this->buildViolation('The value you selected is not a valid choice.')
            ->setParameter('{{ value }}', 'object')
            ->setParameter('{{ choices }}', 'object, object')
            ->setCode(ValidEnumElement::NO_SUCH_CHOICE_ERROR)
            ->assertRaised();
    }
}
