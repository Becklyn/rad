<?php declare(strict_types=1);

namespace Becklyn\Rad\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

/**
 * Type that maps a PHP object to a clob SQL type.
 */
class SerializedType extends Type
{
    public const NAME = "serialized";


    /**
     * @inheritDoc
     */
    public function getSQLDeclaration (array $fieldDeclaration, AbstractPlatform $platform) : string
    {
        return $platform->getClobTypeDeclarationSQL($fieldDeclaration);
    }


    /**
     * @inheritDoc
     */
    public function convertToDatabaseValue ($value, AbstractPlatform $platform) : string
    {
        return \base64_encode(\serialize($value));
    }


    /**
     * @inheritDoc
     */
    public function convertToPHPValue ($value, AbstractPlatform $platform) : ?string
    {
        if (null === $value)
        {
            return null;
        }

        $value = \is_resource($value) ? \stream_get_contents($value) : $value;

        \set_error_handler(function (int $code, string $message) : bool {
            throw ConversionException::conversionFailedUnserialization($this->getName(), $message);
        });

        try
        {
            /** @noinspection UnserializeExploitsInspection */
            return \unserialize(\base64_decode($value, true));
        }
        finally
        {
            \restore_error_handler();
        }
    }


    /**
     * @inheritDoc
     */
    public function getName () : string
    {
        return self::NAME;
    }


    /**
     * @inheritDoc
     */
    public function requiresSQLCommentHint (AbstractPlatform $platform) : bool
    {
        return true;
    }
}
