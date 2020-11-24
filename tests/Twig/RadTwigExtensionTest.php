<?php declare(strict_types=1);

namespace Tests\Becklyn\Rad\Twig;

use Becklyn\Rad\Exception\UnexpectedTypeException;
use Becklyn\Rad\Html\DataContainer;
use Becklyn\Rad\Translation\DeferredTranslation;
use Becklyn\Rad\Twig\RadTwigExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @group twig
 */
class RadTwigExtensionTest extends TestCase
{
    public function testAppendToKey () : void
    {
        $extension = new RadTwigExtension(
            new DataContainer(),
            $this->createMock(TranslatorInterface::class)
        );
        $array = [
            "existing" => "abc",
        ];

        self::assertSame("abc 123", $extension->appendToKey($array, "existing", "123")["existing"]);
        self::assertSame("123", $extension->appendToKey($array, "missing", "123")["missing"]);
    }


    /**
     * @return array
     */
    public function provideClassnames () : array
    {
        return [
            "simple" => [
                ["a" => true, "b" => true],
                "a b",
            ],
            "numbers" => [
                ["zero" => 0, "one" => 1, "two" => 2],
                "one two",
            ],
            "bool" => [
                ["true" => true, "false" => false, "null" => null],
                "true",
            ],
            "empty" => [
                [],
                "",
            ],
            "keyless values" => [
                ["a" => true, "b", "c" => false, "d", "e" => true],
                "a b d e",
            ],
        ];
    }


    /**
     * @dataProvider provideClassnames
     *
     * @param array  $classnames
     * @param string $expected
     */
    public function testClassnames (array $classnames, string $expected) : void
    {
        $extension = new RadTwigExtension(
            new DataContainer(),
            $this->createMock(TranslatorInterface::class)
        );
        self::assertSame($extension->formatClassNames($classnames), $expected);
    }


    public function provideDeferredTransStringValues () : array
    {
        return [
            "string" => [
                "a.b.c",
                [],
                "messages",
                "foo.bar",
            ],
            "string with arguments" => [
                "a.b.c",
                [
                    "a" => 1,
                    "b" => 2,
                ],
                "backend",
                "baz.bar",
            ],
        ];
    }


    /**
     * @dataProvider provideDeferredTransStringValues
     */
    public function testTransDeferredWithStringValues (string $message, array $arguments, string $domain, string $expected) : void
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator
            ->expects(self::once())
            ->method("trans")
            ->with($message, $arguments, $domain)
            ->willReturn($expected);

        $extension = new RadTwigExtension(new DataContainer(), $translator);

        self::assertSame($extension->transDeferred($message, $arguments, $domain), $expected);
    }


    public function provideDeferredTransWithDeferredTranslation () : array
    {
        return [
            "string" => [
                new DeferredTranslation("a.b.c", [], "messages"),
                "foo.bar",
            ],
            "string with arguments" => [
                new DeferredTranslation("a.b.c", ["a" => 1, "b" => 2], "backend"),
                "baz.bar",
            ],
        ];
    }


    /**
     * @dataProvider provideDeferredTransWithDeferredTranslation
     */
    public function testTransDeferredWithDeferredTranslation (DeferredTranslation $deferredTranslation, string $expected) : void
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator
            ->expects(self::once())
            ->method("trans")
            ->with($deferredTranslation->getId(), $deferredTranslation->getParameters(), $deferredTranslation->getDomain())
            ->willReturn($expected);

        $extension = new RadTwigExtension(new DataContainer(), $translator);

        self::assertSame($extension->transDeferred($deferredTranslation), $expected);
    }
}
