<?php

error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

use Laminas\HttpHandlerRunner\Emitter\EmitterStack;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Laminas\HttpHandlerRunner\Emitter\SapiStreamEmitter;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Place\Controller\PlaceController;
use Place\Service\Notifier;
use Place\Service\Persistence;
use Place\Service\Place;
use Place\Service\Router;
use Place\Service\Template;

require __DIR__ . '/../vendor/autoload.php';

$container = new League\Container\Container();

$container->add(League\Container\Container::class,$container );
$container->add(Template::class);
$container->add(Persistence::class);
$container->add(Place::class)->addArgument(Persistence::class);
$container->add(Router::class)->addArgument(League\Container\Container::class);
$container->add(Notifier::class)->addArgument(Place::class);
$container->add(PlaceController::class)
    ->addArgument(Place::class)
    ->addArgument(Template::class)
    ->addArgument(Notifier::class);

//dev
//$container->get(Place::class)->resetPanel();

$psr17Factory = new Psr17Factory();

$creator = new ServerRequestCreator(
    $psr17Factory, // ServerRequestFactory
    $psr17Factory, // UriFactory
    $psr17Factory, // UploadedFileFactory
    $psr17Factory  // StreamFactory
);

$serverRequest = $creator->fromGlobals();

$response = $container->get(Router::class)->dispatch($serverRequest);

//echo "end";
$stack = new EmitterStack();
$stack->push(new SapiStreamEmitter());

$stack->emit($response);
