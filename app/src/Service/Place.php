<?php

declare(strict_types=1);

namespace Place\Service;

use Cycle\Database\Exception\StatementException;
use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\ORM;
use Place\Entity\Pixel;

class Place
{
    private EntityManagerInterface $entityManager;

    private ORM $orm;

    public function __construct(
        private Persistence $persistence
    )
    {
        $this->orm = $this->persistence->getORM();
        $this->entityManager = $this->persistence->getEntityManager();
    }

    public function resetPanel(): void
    {
        $this->dropAllPixels();
        $width = 100;

        for($i = 0; $i < $width; $i++){
            for($j = 0;$j < $width; $j++){
                $pixel = new Pixel(
                    $i,
                    $j,
                    "#6495ED"
                );
                $pixel->setUpdatedAt(new \DateTimeImmutable());
                $this->entityManager->persist($pixel);
            }
            if($i % $width === 0){
                $this->entityManager->run();
            }
        }
        $this->entityManager->run();

    }

    public function setPixel(Pixel $pixel): void
    {
        $pixel->setUpdatedAt(new \DateTimeImmutable());
        $this->entityManager->persist($pixel);
        $this->entityManager->run();
    }

    public function getPanel(\DateTimeImmutable $dateTimeImmutable = null): array
    {
        $condition = [];
        if($dateTimeImmutable instanceof \DateTimeImmutable){
            $condition = [
                "updatedAt" => ['>=' => $dateTimeImmutable ]
            ];
        }
        return $this->orm->getRepository(Pixel::class)->findAll($condition);
    }

    public function getPixelAt(int $x, int $y): ?Pixel
    {
        return $this->orm->getRepository(Pixel::class)->findOne(
            [
                'x' => $x,
                'y' => $y
            ]);
    }

    private function dropAllPixels(): void
    {
        $source = $this->orm->getSource(Pixel::class);

        $db = $source->getDatabase();

        try{
            $db->execute('DELETE FROM pixels');
        }catch (StatementException $exception){

        }
    }
}
