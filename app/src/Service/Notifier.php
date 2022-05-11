<?php

namespace Place\Service;

use Nyholm\Psr7\Factory\Psr17Factory;
use Place\Service\EventHandler\PixelUpdateNotificationHandler;
use Psr\Http\Message\ResponseInterface;
use Sse\SSE;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Notifier
{
    private SSE $see;

    public function __construct(
        private Place $place
    )
    {
        $this->see = new SSE();
        $this->see->sleep_time = 1; //The time to sleep after the data has been sent in seconds. Default: 0.5.
        $this->see->exec_limit = 100; //the execution time of the loop in seconds. Default: 600. Set to 0 to allow the script to run as long as possible.
        $this->see->client_reconnect = 10; //the time for the client to reconnect after the connection has lost in seconds. Default: 1.
        $this->see->allow_cors = true; //Allow cross-domain access? Default: false. If you want others to access this must set to true.
        $this->see->keep_alive_time = 600; //The interval of sending a signal to keep the connection alive. Default: 300 seconds.
        $this->see->use_chunked_encoding = false; //Use chunked encoding. Some server may get problems with this and it defaults to false
//        $this->see->is_reconnect = false;
        $this->see->content_encoding_none = true;
        $this->see->close_connection = false;
        $this->see->pad_response_data = true;
        $this->see->addEventListener('pixel_updated', new PixelUpdateNotificationHandler($this->place));//register your event handler
    }

    public function createResponse()
    {
        $psr17Factory = new Psr17Factory();
        $response = $this->see->createResponse();

//        $psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
//        $response = $psrHttpFactory->createResponse($response);

        return $response;
    }
}
