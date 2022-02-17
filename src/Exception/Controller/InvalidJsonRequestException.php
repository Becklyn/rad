<?php declare(strict_types=1);

namespace Becklyn\Rad\Exception\Controller;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

final class InvalidJsonRequestException extends \RuntimeException implements HttpExceptionInterface
{
    private int $statusCode;


    /**
     */
    public function __construct (string $message, int $statusCode, ?\Throwable $previous = null)
    {
        parent::__construct($message, $statusCode, $previous);
        $this->statusCode = $statusCode;
    }


    /**
     * @inheritDoc
     */
    public function getStatusCode () : int
    {
        return $this->statusCode;
    }


    /**
     * @inheritDoc
     */
    public function getHeaders () : array
    {
        return [];
    }
}
