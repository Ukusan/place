<?php

namespace Place\Service\Http;

use Laminas\HttpHandlerRunner\Emitter\EmitterStack;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Laminas\HttpHandlerRunner\Emitter\SapiStreamEmitter;
use Psr\Http\Message\ResponseInterface;

class ResponseEmitter
{
    private EmitterStack $emitterStack;

    public function __construct()
    {
        $this->emitterStack = new EmitterStack();
        $this->emitterStack->push(new SapiStreamEmitter());
        $this->emitterStack->push(new SapiEmitter());
    }

    public function emit(ResponseInterface $response): void
    {
        $this->emitterStack->emit($response);
    }
}
