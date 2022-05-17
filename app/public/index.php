<?php declare(strict_types=1);

error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

use Laminas\HttpHandlerRunner\Emitter\EmitterStack;
use Laminas\HttpHandlerRunner\Emitter\SapiStreamEmitter;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Place\Application;
use Place\Controller\PlaceController;
use Place\Service\Http\RequestBuilder;
use Place\Service\Http\ResponseEmitter;
use Place\Service\Notifier;
use Place\Service\Persistence;
use Place\Service\Place;
use Place\Service\Router;
use Place\Service\Template;

require __DIR__ . '/../vendor/autoload.php';

$container = new League\Container\ReflectionContainer();

//dev
//$container->get(Place::class)->resetPanel();

$request = $container->get(RequestBuilder::class)->generateFromGobals();
$response = $container->get(Application::class)->handle($request);
$container->get(ResponseEmitter::class)->emit($response);

