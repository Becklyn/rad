<?php declare(strict_types=1);

namespace Tests\Becklyn\Rad\Entity\Traits;

use Becklyn\Rad\Entity\Traits\IdTrait;
use PHPUnit\Framework\TestCase;

class IdTraitTest extends TestCase
{
    /**
     *
     */
    public function testIsNew () : void
    {
        $new = new class {
            use IdTrait;
        };

        $persisted = new class {
            use IdTrait;

            public function __construct ()
            {
                $this->id = 42;
            }
        };

        self::assertTrue($new->isNew());
        self::assertFalse($persisted->isNew());
    }
}
