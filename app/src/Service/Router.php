<?php

declare(strict_types=1);

namespace Place\Service;

use FastRoute;
use Place\Controller\API\PanelController;
use Place\Service\Routing\Exception\MethodNotAllowedException;
use Place\Service\Routing\Exception\RouteNotFoundException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Router
{

    private FastRoute\Dispatcher $dispatcher;

    public function __construct(
        private readonly ContainerInterface $container
    ){
        $this->dispatcher = new FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $routeCollector) {
            $routeCollector->addGroup('panel', function(FastRoute\RouteCollector $routeCollector) {
                $routeCollector->addRoute('GET', '/', PanelController::class.':panel');
                $routeCollector->addRoute('POST', '/', PanelController::class.':setPixel');
                $routeCollector->addRoute('GET', '/keep-update', PanelController::class.':keepUpdate');
            });
            $routeCollector->addRoute('GET', '/', PanelController::class.':index');
            $routeCollector->addRoute('GET', '/reset', PanelController::class.':reset');
        });
    }

    /**
     * @throws MethodNotAllowedException
     * @throws RouteNotFoundException
     */
    public function dispatch(ServerRequestInterface $serverRequest): ResponseInterface
    {
        $callback = $this->resolveRoute($serverRequest);
        return $this->resolveControllerCall($callback, $serverRequest);
    }

    private function resolveControllerCall(string $callback,ServerRequestInterface $serverRequest): ResponseInterface
    {
        list($controllerName,$methodName) = explode(':',$callback);
        $controller = $this->container->get($controllerName);
        return call_user_func(array($controller, $methodName), $serverRequest);
    }

    private function resolveRoute(ServerRequestInterface $serverRequest): string
    {

        $routeInfo =  $this->dispatcher->dispatch($serverRequest->getMethod(), $serverRequest->getUri()->getPath());

        switch ($routeInfo[0]) {
            case FastRoute\Dispatcher::FOUND:
                $this->addRouteParamsToRequest($routeInfo, $serverRequest);
                return $routeInfo[1];
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                throw new MethodNotAllowedException();
            default:
                throw new RouteNotFoundException();
        }
    }

    /**
     * @param array $routeInfo
     * @param mixed $serverRequest
     * @return ServerRequestInterface
     */
    protected function addRouteParamsToRequest(array $routeInfo, mixed $serverRequest): ServerRequestInterface
    {
        if (isset($routeInfo[2])) {
            foreach ($routeInfo[2] as $name => $pathParam) {
                $serverRequest = $serverRequest->withAttribute($name, $pathParam);
            }
        }
        return $serverRequest;
    }
}
