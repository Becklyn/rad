<?php declare(strict_types=1);

namespace Becklyn\Rad\Tags;

use Becklyn\Rad\Exception\TagNormalizationException;
use Doctrine\ORM\QueryBuilder;

/**
 * Helper class, that applies a filter to a query build to select for the given tags.
 */
class TagMatcher
{
    public const HAS_ANY_TAG = false;
    public const HAS_ALL_TAGS = true;

    private readonly array $tags;

    private bool $mustJoin = true;


    public function __construct (iterable $tags, private readonly string $selector, private readonly bool $selectionMode = self::HAS_ALL_TAGS)
    {
        $this->tags = self::normalizeTagList($tags);
    }


    /**
     * @return $this
     */
    public function mustJoin (bool $mustJoin) : self
    {
        $this->mustJoin = $mustJoin;
        return $this;
    }


    public function apply (QueryBuilder $queryBuilder) : void
    {
        if (self::HAS_ALL_TAGS === $this->selectionMode)
        {
            $this->applyHasAll($queryBuilder);
        }
        else
        {
            \assert(self::HAS_ANY_TAG === $this->selectionMode);
            $this->applyHasAny($queryBuilder);
        }
    }


    /**
     */
    private function applyHasAll (QueryBuilder $queryBuilder) : void
    {
        foreach ($this->tags as $index => $tag)
        {
            $queryBuilder
                ->leftJoin($this->selector, "__tag{$index}")
                ->andWhere("__tag{$index}.tag = :__tag{$index}")
                ->setParameter("__tag{$index}", $tag);
        }
    }


    /**
     */
    private function applyHasAny (QueryBuilder $queryBuilder) : void
    {
        if ($this->mustJoin)
        {
            $queryBuilder
                ->leftJoin($this->selector, "__tag")
                ->addSelect("__tag");

            $selector = "__tag";
        }
        else
        {
            $selector = $this->selector;
        }


        if (!empty($this->tags))
        {
            $queryBuilder
                ->andWhere("{$selector}.tag IN (:__tags)")
                ->setParameter("__tags", $this->tags);
        }
    }


    /**
     * Normalizes the tag list.
     *
     * @param iterable<string|TagInterface|mixed> $tags
     *
     * @return string[]
     */
    public static function normalizeTagList (iterable $tags) : array
    {
        $labels = [];

        foreach ($tags as $tag)
        {
            if (null === $tag)
            {
                continue;
            }

            if ($tag instanceof TagInterface)
            {
                $labels[] = $tag->getTagLabel();
                continue;
            }

            if (\is_string($tag))
            {
                $labels[] = $tag;
                continue;
            }

            throw new TagNormalizationException(\sprintf(
                "Can't transform value of type '%s'.",
                \get_debug_type($tag)
            ));
        }

        return $labels;
    }
}
