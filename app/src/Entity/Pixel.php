<?php

declare(strict_types=1);

namespace Place\Entity;

use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Column;
use DateTimeImmutable;

#[Entity]
class Pixel
{

    #[Column(type: "datetime")]
    private DateTimeImmutable $updatedAt;

    public function __construct(
        #[Column(type: 'int', primary: true)]
        private int $x,

        #[Column(type: 'int', primary: true)]
        private int $y,

        #[Column(type: "string")]
        private string $color,
    ){}

    /**
     * @return int
     */
    public function getX(): int
    {
        return $this->x;
    }

    /**
     * @return int
     */
    public function getY(): int
    {
        return $this->y;
    }

    /**
     * @param string $color
     */
    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTimeImmutable $updatedAt
     */
    public function setUpdatedAt(DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
