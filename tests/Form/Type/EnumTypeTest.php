<?php

declare(strict_types=1);

namespace Zlikavac32\SymfonyEnum\Tests\Form\Type;

use LogicException;
use stdClass;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\Test\TypeTestCase;
use Zlikavac32\Enum\Enum;
use Zlikavac32\SymfonyEnum\Form\Type\EnumType;
use Zlikavac32\SymfonyEnum\Tests\Fixtures\YesNoEnum;

class EnumTypeTest extends TypeTestCase
{

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Option choices is overridden by the type so don't pass your own value
     */
    public function testThatPassingChoicesOptionThrowsException(): void
    {
        $this->factory->create(
            EnumType::class,
            null,
            [
                'enum_class' => YesNoEnum::class,
                'choices'    => [1],
            ]
        );
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Option choice_loader is overridden by the type so don't pass your own value
     */
    public function testThatPassingChoiceLoaderOptionThrowsException(): void
    {
        $this->factory->create(
            EnumType::class,
            null,
            [
                'enum_class'    => YesNoEnum::class,
                'choice_loader' => $this->createMock(ChoiceLoaderInterface::class),
            ]
        );
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage stdClass does not have Zlikavac32\Enum\Enum as it's parent
     */
    public function testThatEnumClassMustBeValid(): void
    {
        $this->factory->create(
            EnumType::class,
            null,
            [
                'enum_class' => stdClass::class,
            ]
        );
    }

    public function testThatValidNameCanBeSubmitted(): void
    {
        $form = $this->factory->create(
            EnumType::class,
            null,
            [
                'enum_class' => YesNoEnum::class,
            ]
        );

        $form->submit('YES');

        $this->assertSame(YesNoEnum::YES(), $form->getData());
    }

    public function testThatInvalidNameCanNotBeSubmitted(): void
    {
        $form = $this->factory->create(
            EnumType::class,
            null,
            [
                'enum_class' => YesNoEnum::class,
            ]
        );

        $form->submit('I_DONT_EXIST');

        $this->assertNull($form->getData());
    }

    public function testThatViewChoicesAreCorrect(): void
    {
        $form = $this->factory->create(
            EnumType::class,
            null,
            [
                'enum_class' => YesNoEnum::class,
            ]
        );

        $view = $form->createView();

        $this->assertEquals(
            [
                new ChoiceView(YesNoEnum::NO(), 'NO', 'NO'),
                new ChoiceView(YesNoEnum::YES(), 'YES', 'YES'),
            ],
            $view->vars['choices']
        );
    }

    public function testThatCustomValueCallbackCanBePassed(): void
    {
        $form = $this->factory->create(
            EnumType::class,
            null,
            [
                'enum_class'   => YesNoEnum::class,
                'choice_value' => function (?Enum $enum): string {
                    if (null === $enum) {
                        return '';
                    }

                    return (string) $enum->ordinal();
                },
            ]
        );

        $view = $form->createView();

        $this->assertEquals(
            [
                new ChoiceView(YesNoEnum::NO(), '0', 'NO'),
                new ChoiceView(YesNoEnum::YES(), '1', 'YES'),
            ],
            $view->vars['choices']
        );
    }

    public function testThatCustomLabelCallbackCanBePassed(): void
    {
        $form = $this->factory->create(
            EnumType::class,
            null,
            [
                'enum_class'   => YesNoEnum::class,
                'choice_label' => function (?Enum $enum): string {
                    if (null === $enum) {
                        return '';
                    }

                    return strtolower($enum->name());
                },
            ]
        );

        $view = $form->createView();

        $this->assertEquals(
            [
                new ChoiceView(YesNoEnum::NO(), 'NO', 'no'),
                new ChoiceView(YesNoEnum::YES(), 'YES', 'yes'),
            ],
            $view->vars['choices']
        );
    }
}
