<?php declare(strict_types=1);

namespace Tests\Becklyn\Rad\Sortable;

use Becklyn\Rad\Entity\Interfaces\SortableEntityInterface;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;

trait SortableTestTrait
{
    private function createEntity (int $id, ?int $sortOrder = null) : SortableEntityInterface
    {
        return new class ($id, $sortOrder) implements SortableEntityInterface
        {

            public function __construct(private readonly int $id, private ?int $sortOrder = null)
            {
            }


            public function getId () : int
            {
                return $this->id;
            }


            public function isNew () : bool
            {
                return false;
            }


            public function getSortOrder () : ?int
            {
                return $this->sortOrder;
            }


            public function setSortOrder (int $sortOrder) : void
            {
                $this->sortOrder = $sortOrder;
            }
        };
    }

    private function createEntityWithProperties (int $id, $a, $b, $c, ?int $sortOrder = null) : SortableEntityInterface
    {
        return new class ($id, $sortOrder, $a, $b, $c) implements SortableEntityInterface
        {
            public function __construct(private readonly int $id, private ?int $sortOrder, private $a, private $b, private $c)
            {
            }


            public function getId () : int
            {
                return $this->id;
            }


            public function isNew () : bool
            {
                return null !== $this->id;
            }


            public function getSortOrder () : ?int
            {
                return $this->sortOrder;
            }


            public function setSortOrder (int $sortOrder) : void
            {
                $this->sortOrder = $sortOrder;
            }

            public function getA ()
            {
                return $this->a;
            }

            public function getB ()
            {
                return $this->b;
            }

            public function getC ()
            {
                return $this->c;
            }
        };
    }


    private function createEntities (int $number) : array
    {
        $result = [];

        for ($i = 0; $i < $number; $i++)
        {
            $result[] = $this->createEntity($i, $i);
        }

        return $result;
    }


    /**
     * @param SortableEntityInterface[] $entities
     */
    private function mapEntities (array $entities) : array
    {
        $result = [];

        foreach ($entities as $entity)
        {
            $result[$entity->getId()] = $entity->getSortOrder();
        }

        return $result;
    }


    /**
     */
    private function createIteratingRepository (SortableEntityInterface ...$entities) : array
    {
        $repository = $this->createMock(EntityRepository::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(AbstractQuery::class);

        $repository
            ->method("createQueryBuilder")
            ->willReturn($queryBuilder);

        $queryBuilder
            ->method("select")
            ->willReturnSelf();

        $queryBuilder
            ->method("addOrderBy")
            ->willReturnSelf();

        $queryBuilder
            ->method("expr")
            ->willReturn(new Expr());

        $queryBuilder
            ->method("getQuery")
            ->willReturn($query);

        \usort(
            $entities,
            static fn (SortableEntityInterface $left, SortableEntityInterface $right): int => $left->getSortOrder() - $right->getSortOrder()
        );

        $query
            ->method("toIterable")
            ->willReturn($entities);

        return [$repository, $queryBuilder];
    }
}
