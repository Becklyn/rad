<?php declare(strict_types=1);

namespace Becklyn\Rad;

use Becklyn\Rad\DependencyInjection\BecklynRadExtension;
use Becklyn\Rad\DependencyInjection\DoctrineExtensionsCompilerPass;
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
        return new BecklynRadExtension($this);
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
}
