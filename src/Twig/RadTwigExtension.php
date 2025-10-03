<?php declare(strict_types=1);

namespace Becklyn\Rad\Twig;

use Becklyn\Rad\Exception\UnexpectedTypeException;
use Becklyn\Rad\Html\DataContainer;
use Becklyn\Rad\Route\LinkableHandlerInterface;
use Becklyn\Rad\Route\LinkableInterface;
use Becklyn\Rad\Translation\DeferredTranslation;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class RadTwigExtension extends AbstractExtension
{
    public function __construct(private readonly DataContainer $dataContainer, private readonly TranslatorInterface $translator, private readonly ?LinkableHandlerInterface $linkableHandler = null)
    {
    }


    public function appendToKey (array $map, string $key, string $append) : array
    {
        $value = $map[$key] ?? "";
        $map[$key] = \trim(\sprintf('%s %s', $value, $append));
        return $map;
    }


    public function formatClassNames (array $classes) : string
    {
        $result = [];

        foreach ($classes as $class => $enabled)
        {
            // support key less values
            if (\is_int($class))
            {
                $result[] = $enabled;
            }
            elseif ($enabled)
            {
                $result[] = $class;
            }
        }

        return \implode(" ", $result);
    }


    /**
     * @param string|DeferredTranslation|null $message Translation key #TranslationKey or a DeferredTranslation
     * @param string                          $domain  Translation domain #TranslationDomain
     */
    public function transDeferred (
        $message,
        array $arguments = [],
        string $domain = "messages"
    ) : string
    {
        if (null === $message || "" === $message)
        {
            return "";
        }

        if (!\is_string($message) && !$message instanceof DeferredTranslation)
        {
            throw new UnexpectedTypeException($message, DeferredTranslation::class . " or string");
        }

        $deferredTranslation = $message instanceof DeferredTranslation
            ? $message
            : new DeferredTranslation($message, $arguments, $domain);

        return DeferredTranslation::translateValue($deferredTranslation, $this->translator);
    }


    /**
     * @param string|LinkableInterface|null $link
     */
    public function linkableUrl ($link) : ?string
    {
        if (null === $this->linkableHandler)
        {
            throw new \LogicException("Could not generate URL for LinkableInterface as no default LinkableHandlerInterface service has been registered.");
        }

        try
        {
            $this->linkableHandler->ensureValidLinkTarget($link);

            return $this->linkableHandler->generateUrl($link);
        }
        catch (UnexpectedTypeException $unexpectedTypeException)
        {
            throw new \LogicException("Could not generate URL for LinkableInterface due to an error.", 500, $unexpectedTypeException);
        }
    }


    /**
     * @inheritDoc
     */
    public function getFunctions () : array
    {
        $safeHtml = ["is_safe" => ["html"]];

        return [
            new TwigFunction("classnames", [$this, "formatClassNames"]),
            new TwigFunction("data_container", [$this->dataContainer, "renderToHtml"], $safeHtml),
            new TwigFunction("linkable_url", [$this, "linkableUrl"]),
        ];
    }


    /**
     * @inheritDoc
     */
    public function getFilters () : array
    {
        return [
            new TwigFilter("appendToKey", [$this, "appendToKey"]),
            new TwigFilter("transDeferred", [$this, "transDeferred"]),
        ];
    }
}
