<?php

declare(strict_types=1);

namespace Place\Controller\API;

use Nyholm\Psr7\Factory\Psr17Factory;
use Place\Entity\Pixel;
use Place\Service\Notifier;
use Place\Service\Place;
use Place\Service\Template;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PanelController
{

    public function __construct(
        private Place $place,
        private Template $template,
        private Notifier $notifier
    ){

    }

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $pixels = $this->place->getPanel();
        return $this->createResponse(200, $this->template->render('index.tpl',['pixels' => $pixels]));
    }

    public function keepUpdate(ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->notifier->createResponse();
        $response->send();
        exit;
//        return $this->notifier->createResponse();
    }

    public function panel(ServerRequestInterface $request): ResponseInterface
    {
        $pixels = $this->place->getPanel();

        $formatted = array_map(function(Pixel $pixel){
            return [
                'x' => $pixel->getX(),
                'y' => $pixel->getY(),
                'color' => $pixel->getColor()
            ];
        }, $pixels);

        return $this->createResponse(200, $formatted);
    }

    public function reset(ServerRequestInterface $request): ResponseInterface
    {
        $this->place->resetPanel();

        return $this->createResponse(200, null);
    }

    public function setPixel(ServerRequestInterface $request): ResponseInterface
    {
        $post = $request->getParsedBody();
        $x = (int) $post['x'];
        $y = (int) $post['y'];
        $color = $post['color'];

        $pixel = $this->place->getPixelAt($x, $y);

        if(!$pixel instanceof Pixel){
            return $this->createResponse(404);
        }

        $pixel->setColor($color);
        $this->place->setPixel($pixel);

        return $this->createResponse(200, [
            'x' => $pixel->getX(),
            'y' => $pixel->getY(),
            'color' => $pixel->getColor(),
            'updatedAt' => $pixel->getUpdatedAt()->format(DATE_ATOM)
        ]);
    }

    private function createResponse(int $returnCode, $body = null): ResponseInterface
    {
        $psr17Factory = new Psr17Factory();
        if(is_array($body)){
            $body = json_encode($body);
        }
        if(!is_string($body)){
            $body = "";
        }
        $responseBody = $psr17Factory->createStream($body);
        return $psr17Factory->createResponse($returnCode)->withBody($responseBody);
    }
}
