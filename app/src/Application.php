<?php

declare(strict_types=1);

namespace Place;

use Place\Service\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Application implements RequestHandlerInterface
{

    public function __construct(
        private readonly Router $router
    ){

    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->router->dispatch($request);
    }
}
