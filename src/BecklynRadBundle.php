<?php declare(strict_types=1);

namespace Becklyn\Rad;

use Becklyn\Rad\DependencyInjection\DoctrineExtensionsCompilerPass;
use Becklyn\Rad\Doctrine\Types\SerializedType;
use Becklyn\RadBundles\Bundle\BundleExtension;
use Doctrine\DBAL\Types\Type;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 *
 */
class BecklynRadBundle extends Bundle
{
    /**
     * @inheritDoc
     */
    public function getContainerExtension ()
    {
        return new BundleExtension($this);
    }


    /**
     */
    public function build (ContainerBuilder $container) : void
    {
        $container->addCompilerPass(new DoctrineExtensionsCompilerPass());
    }


    /**
     * @inheritDoc
     */
    public function getPath () : string
    {
        return \dirname(__DIR__);
    }


    /**
     * @inheritDoc
     */
    public function boot () : void
    {
        parent::boot();

        Type::addType(SerializedType::NAME, SerializedType::class);
    }
}
