<?php declare(strict_types=1);

namespace Becklyn\Rad\Exception;

/**
 * Like {@see EntityRemovalBlockedException}, except that this exception carries a frontend message.
 */
class LabeledEntityRemovalBlockedException extends EntityRemovalBlockedException implements LabeledExceptionInterface
{
    /**
     * {@inheritdoc}
     *
     * The $frontendMessage must be a message key from the default message domain that can be translated to the user message.
     *
     * @param object|object[] $entities
     * @param string          $frontendMessage the #TranslationKey to use
     */
    public function __construct ($entities, string $message, private readonly string $frontendMessage, ?\Throwable $previous = null)
    {
        parent::__construct($entities, $message, $previous);
    }

    /**
     */
    public function getFrontendMessage () : string
    {
        return $this->frontendMessage;
    }
}
