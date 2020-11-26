<?php declare(strict_types=1);

namespace Becklyn\Rad\Route;

interface LinkableHandlerInterface
{
    public const OPTIONAL = true;
    public const REQUIRED = false;

    /**
     * @param string|LinkableInterface|null $link
     */
    public function generateUrl ($link) : ?string;


    /**
     * @param mixed $link
     */
    public function isValidLinkTarget ($link, bool $isOptional = self::REQUIRED) : bool;


    /**
     * @param mixed $link
     */
    public function ensureValidLinkTarget ($link, bool $isOptional = self::REQUIRED) : void;
}
