<?php

declare(strict_types=1);

namespace Zlikavac32\SymfonyEnum\Validator\Constraints;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class ValidEnumElement extends AbstractEnumConstraint
{
    public function __construct($options = null)
    {
        parent::__construct(
            function (): array {
                return $this->enumClass::values();
            },
            $options
        );
    }
}
