<?php declare(strict_types=1);

namespace Becklyn\RadBundle\Translation;

use Becklyn\RadBundle\Exception\InvalidTranslationActionException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * A deferred translation.
 *
 * Basically a VO encapsulating all of the logic required for translating a key, but without directly translating it.
 * Instead the translation can be deferred to a later point in time (this way you don't need a dependency on the
 * translator right away).
 */
class DeferredTranslation
{
    /** @var string */
    private $id;

    /** @var array */
    private $parameters;

    /** @var string */
    private $domain;


    /**
     * @param string $id         Translation key #TranslationKey
     * @param array  $parameters Translation parameters
     * @param string $domain     Translation domain #TranslationDomain
     */
    public function __construct (string $id, array $parameters = [], string $domain = "messages")
    {
        $this->id = $id;
        $this->parameters = $parameters;
        $this->domain = $domain;
    }


    /**
     */
    public function getId () : string
    {
        return $this->id;
    }


    /**
     */
    public function getParameters () : array
    {
        return $this->parameters;
    }


    /**
     */
    public function getDomain () : string
    {
        return $this->domain;
    }


    /**
     * @return static
     */
    public function withParameters (array $parameters) : self
    {
        $modified = clone $this;
        $modified->parameters = \array_replace($modified->parameters, $parameters);
        return $modified;
    }


    /**
     * Translates the given string
     */
    public function translate (TranslatorInterface $translator) : string
    {
        return $translator->trans(
            $this->id,
            $this->parameters,
            $this->domain
        );
    }


    /**
     * @param self|string|mixed|null $value
     */
    public static function translateValue ($value, TranslatorInterface $translator) : string
    {
        if (\is_string($value) || null === $value)
        {
            return (string) $value;
        }

        if ($value instanceof self)
        {
            return $value->translate($translator);
        }

        throw new InvalidTranslationActionException(\sprintf(
            "Can't translate value of type '%s', only DeferredTranslations, strings and null are allowed.",
            \is_object($value) ? \get_class($value) : \gettype($value)
        ));
    }


    /**
     * Translates all values
     *
     * @param array<self|string|mixed|null> $values
     *
     * @return string[]
     */
    public static function translateAllValues (array $values, TranslatorInterface $translator) : array
    {
        $result = [];

        foreach ($values as $key => $value)
        {
            $result[$key] = self::translateValue($values, $translator);
        }

        return $result;
    }
}
