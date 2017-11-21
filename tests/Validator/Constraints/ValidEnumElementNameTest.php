<?php

declare(strict_types=1);

namespace Zlikavac32\SymfonyEnum\Tests\Validator\Constraints;

use Symfony\Component\Validator\Constraints\ChoiceValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;
use Zlikavac32\SymfonyEnum\Tests\Fixtures\YesNoEnum;
use Zlikavac32\SymfonyEnum\Validator\Constraints\ValidEnumElementName;

class ValidEnumElementNameTest extends ConstraintValidatorTestCase
{

    protected function createValidator()
    {
        return new ChoiceValidator();
    }

    public function testNullIsValid()
    {
        $this->validator->validate(
            null,
            new ValidEnumElementName(
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
            'YES',
            new ValidEnumElementName(YesNoEnum::class)
        );

        $this->assertNoViolation();
    }

    public function testThatInvalidInstanceBuildsViolation()
    {
        $invalidValue = 'MAYBE';

        $this->validator->validate(
            $invalidValue,
            new ValidEnumElementName(
                [
                    'enumClass' => YesNoEnum::class,
                ]
            )
        );


        $this->buildViolation('The value you selected is not a valid choice.')
            ->setParameter('{{ value }}', '"MAYBE"')
            ->setCode(ValidEnumElementName::NO_SUCH_CHOICE_ERROR)
            ->assertRaised();
    }
}
