<?php

namespace Place\Service\Http;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Http\Message\ServerRequestInterface;

class RequestBuilder
{

    private ServerRequestCreator $creator;

    public function __construct(){
        $factory = new Psr17Factory();

        $this->creator = new ServerRequestCreator(
            $factory, // ServerRequestFactory
            $factory, // UriFactory
            $factory, // UploadedFileFactory
            $factory  // StreamFactory
        );
    }

    public function generateFromGobals(): ServerRequestInterface
    {
        return $this->creator->fromGlobals();
    }
}
