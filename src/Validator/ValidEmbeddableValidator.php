<?php declare(strict_types=1);

namespace Becklyn\Rad\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ValidEmbeddableValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint) : void
    {
        if (!$constraint instanceof ValidEmbeddable)
        {
            throw new UnexpectedTypeException($constraint, ValidEmbeddable::class);
        }

        if (null === $value)
        {
            return;
        }

        $this->context
            ->getValidator()
            ->inContext($this->context)
            ->validate($value, null, $constraint->embeddedGroups);
    }
}
