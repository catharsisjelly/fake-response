<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestConfigMiddleware implements MiddlewareInterface
{
    /**
     * @var array
     */
    private $requestConfig;

    /**
     * @param array|null $requestConfig
     */
    public function __construct(?array $requestConfig)
    {
        $this->requestConfig = $requestConfig;
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $response = $handler->handle($request);

        if (!$this->requestConfig) {
            return $response;
        }

        if ($this->requestConfig['headers']) {
            foreach ($this->requestConfig['headers'] as $header => $value) {
                if (!$request->hasHeader($header) ) {
                    return $response->withStatus(401);
                }

                if ($request->getHeaderLine($header) !== $value) {
                    return $response->withStatus(401);
                }
            }
        }

        return $response;
    }
}
