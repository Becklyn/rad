<?php declare(strict_types=1);

namespace Becklyn\Rad\DependencyInjection;

use Becklyn\Rad\Doctrine\Types\SerializedType;
use Becklyn\RadBundles\Bundle\BundleExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

final class BecklynRadExtension extends BundleExtension implements PrependExtensionInterface
{
    /**
     * @inheritDoc
     */
    public function prepend (ContainerBuilder $container) : void
    {
        $container->prependExtensionConfig("doctrine", [
            "dbal" => [
                "types" => [
                    SerializedType::NAME => SerializedType::class,
                ],
            ],
        ]);
    }
}
