<?php

namespace Place\Service\EventHandler;

use Cycle\ORM\ORM;
use Place\Entity\Pixel;
use Place\Service\Place;
use Sse\Event;

class PixelUpdateNotificationHandler implements Event
{
    private \DateTimeImmutable $lastUpdated;
    public function __construct(
        private Place $place
    )
    {
        $this->lastUpdated = new \DateTimeImmutable();
    }

    public function update(){
        $newPixels = $this->place->getPanel($this->lastUpdated);
        $this->lastUpdated = new \DateTimeImmutable();
        return json_encode(
            array_map(function(Pixel $pixel){
                return [
                    'x' => $pixel->getX(),
                    'y' => $pixel->getY(),
                    'color' => $pixel->getColor()
                ];
            },$newPixels)
        );
    }

    public function check(){
        return !empty($this->place->getPanel($this->lastUpdated));
    }
}
