<?php

declare(strict_types=1);

namespace Zlikavac32\SymfonyEnum\Tests\Validator\Constraints;

use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Validator\Constraints\ChoiceValidator;
use Zlikavac32\SymfonyEnum\Tests\Fixtures\YesNoEnum;
use Zlikavac32\SymfonyEnum\Validator\Constraints\AbstractEnumConstraint;

class AbstractEnumConstraintTest extends TestCase
{
    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Enum class can not be null
     */
    public function testThatEnumClassCanNotBeNull(): void
    {
        $this->create(null);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Provided enum class stdClass is not valid
     */
    public function testThatEnumClassMustHaveEnumAsItsParent(): void
    {
        $this->create(stdClass::class);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Key choices is overridden internally so it should not be set from the outside
     */
    public function testThatChoicesOptionMustNotBeSet(): void
    {
        $this->create(
            [
                'choices' => [
                    'YES',
                ],
            ]
        );
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Key callback is overridden internally so it should not be set from the outside
     */
    public function testThatCallbackOptionMustNotBeSet(): void
    {
        $this->create(
            [
                'callback' => function () {
                },
            ]
        );
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Key strict is overridden internally so it should not be set from the outside
     */
    public function testThatStrictOptionMustNotBeSet(): void
    {
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
