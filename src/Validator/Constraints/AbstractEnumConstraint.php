<?php

declare(strict_types=1);

namespace Zlikavac32\SymfonyEnum\Validator\Constraints;

use Closure;
use LogicException;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\ChoiceValidator;
use function Zlikavac32\Enum\assertFqnIsEnumClass;
use Zlikavac32\Enum\Enum;

abstract class AbstractEnumConstraint extends Choice
{
    /**
     * @var string|Enum
     */
    public $enumClass;

    public function __construct(Closure $callback, $options = null)
    {
        if (false === is_array($options)) {
            $options = ['enumClass' => $options];
        }

        $this->assertThatOverriddenKeysAreNotSet($options);

        parent::__construct(['strict' => true, 'callback' => $callback] + $options);

        $this->assertEnumClassIsValid($this->enumClass);
    }

    public function validatedBy()
    {
        return ChoiceValidator::class;
    }

    private function assertThatOverriddenKeysAreNotSet(array $options): void
    {
        foreach (['choices', 'callback', 'strict'] as $key) {
            if (array_key_exists($key, $options)) {
                throw new LogicException(
                    sprintf('Key %s is overridden internally so it should not be set from the outside', $key)
                );
            }
        }
    }

    private function assertEnumClassIsValid(?string $enumClass): void
    {
        if (null === $enumClass) {
            throw new LogicException('Enum class can not be null');
        }

        assertFqnIsEnumClass($enumClass);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOption()
    {
        return 'enumClass';
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredOptions()
    {
        return ['enumClass'];
    }
}
