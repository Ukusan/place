<?php

namespace Place\Service;

use FastRoute;
use League\Container\Container;
use Nyholm\Psr7\Factory\Psr17Factory;
use Place\Controller\PlaceController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Router
{
    public function __construct(
        private Container $container
    ){

    }

    public function dispatch(ServerRequestInterface $serverRequest): ResponseInterface
    {
        $psr17Factory = new Psr17Factory();
        $routeInfo =  $this->getDispatcher()->dispatch($serverRequest->getMethod(), $serverRequest->getUri()->getPath());
        if(isset($routeInfo[2])){
            foreach ($routeInfo[2] as $name => $pathParam) {
                $serverRequest = $serverRequest->withAttribute($name,$pathParam);
            }
        }

        switch ($routeInfo[0]) {
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $response = $psr17Factory->createResponse(405);
                break;
            case FastRoute\Dispatcher::FOUND:
                list($controllerName,$methodName) = explode(':',$routeInfo[1]);
                $controller = $this->container->get($controllerName);
                $response = call_user_func(array($controller, $methodName), $serverRequest);
                break;
            default:
                $response = $psr17Factory->createResponse(404);
                break;
        }
        return $response;
    }

    private function getDispatcher(): FastRoute\Dispatcher
    {
        return FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
            $r->addRoute('GET', '/', PlaceController::class.':index');
            $r->addRoute('GET', '/sse', PlaceController::class.':sse');
            $r->addRoute('GET', '/panel', PlaceController::class.':panel');
            $r->addRoute('POST', '/panel', PlaceController::class.':setPixel');
            $r->addRoute('GET', '/panel/{x:\d+}/{y:\d+}', PlaceController::class.':getPixel');
        });
    }
}
