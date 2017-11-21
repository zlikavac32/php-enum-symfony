<?php

declare(strict_types=1);

namespace Zlikavac32\SymfonyEnum\Validator\Constraints;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class ValidEnumElementName extends AbstractEnumConstraint
{
    public function __construct($options = null)
    {
        parent::__construct(
            function (): array {
                $choices = [];

                foreach ($this->enumClass::values() as $element) {
                    $name = $element->name();
                    $choices[] = $name;
                }

                return $choices;
            },
            $options
        );
    }
}
