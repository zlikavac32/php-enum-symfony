<?php

declare(strict_types=1);

namespace Zlikavac32\SymfonyEnum\Tests\Validator\Constraints;

use LogicException;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Validator\Constraints\ChoiceValidator;
use Zlikavac32\SymfonyEnum\Tests\Fixtures\YesNoEnum;
use Zlikavac32\SymfonyEnum\Validator\Constraints\AbstractEnumConstraint;

class AbstractEnumConstraintTest extends TestCase
{

    public function testThatEnumClassCanNotBeNull(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Enum class can not be null');

        $this->create(null);
    }

    public function testThatEnumClassMustHaveEnumAsItsParent(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('stdClass does not have Zlikavac32\Enum\Enum as it\'s parent');

        $this->create(stdClass::class);
    }

    public function testThatChoicesOptionMustNotBeSet(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Key choices is overridden internally so it should not be set from the outside');

        $this->create(
            [
                'choices' => [
                    'YES',
                ],
            ]
        );
    }

    public function testThatCallbackOptionMustNotBeSet(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Key callback is overridden internally so it should not be set from the outside');

        $this->create(
            [
                'callback' => function () {
                },
            ]
        );
    }

    public function testThatStrictOptionMustNotBeSet(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Key strict is overridden internally so it should not be set from the outside');

        $this->create(
            [
                'strict' => false,
            ]
        );
    }

    public function testThatConstraintCanBeCreatedWithJustEnumClass(): void
    {
        $this->assertInstanceOf(AbstractEnumConstraint::class, $this->create(YesNoEnum::class));
    }

    public function testThatConstraintCanBeCreatedWithOptionsArray(): void
    {
        $this->assertInstanceOf(
            AbstractEnumConstraint::class,
            $this->create(
                [
                    'enumClass' => YesNoEnum::class,
                ]
            )
        );
    }

    public function testThatDefaultOptionIsCorrect(): void
    {
        $this->assertSame('enumClass',
            $this->create(YesNoEnum::class)
                ->getDefaultOption()
        );
    }

    public function testThatValidatedByIsCorrect(): void
    {
        $this->assertSame(ChoiceValidator::class,
            $this->create(YesNoEnum::class)
                ->validatedBy()
        );
    }

    public function testThatRequiredOptionsAreCorrect(): void
    {
        $this->assertSame(['enumClass'],
            $this->create(YesNoEnum::class)
                ->getRequiredOptions()
        );
    }

    private function create($options): AbstractEnumConstraint
    {
        return new class (function (): array {
            return ['YES'];
        }, $options) extends AbstractEnumConstraint
        {
        };
    }
}
