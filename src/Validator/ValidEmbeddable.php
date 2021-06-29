<?php declare(strict_types=1);

namespace Becklyn\Rad\Validator;

use Symfony\Component\Validator\Constraints\Valid;

/**
 * @Annotation()
 */
class ValidEmbeddable extends Valid
{
    public array $embeddedGroups = ["Default"];


    /**
     * @inheritDoc
     */
    public function getTargets () : array
    {
        return [
            self::PROPERTY_CONSTRAINT,
        ];
    }
}
