<?php declare(strict_types=1);

namespace Becklyn\Rad\Integration;

use Symfony\Component\HttpKernel\Profiler\Profiler as SymfonyProfiler;

class Profiler
{
    public function __construct(private readonly ?SymfonyProfiler $profiler)
    {
    }


    /**
     *
     */
    public function disable () : void
    {
        if (null !== $this->profiler)
        {
            $this->profiler->disable();
        }
    }
}
