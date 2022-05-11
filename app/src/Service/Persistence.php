<?php

namespace Place\Service;

use Cycle\Annotated;
use Cycle\Database;
use Cycle\Database\Config;
use Cycle\ORM;
use Cycle\ORM\EntityManager;
use Cycle\Schema;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Spiral\Tokenizer;

class Persistence
{
    private const DATABASE_PATH = __DIR__ . '/../../database.sqlite';
    private const ENTITY_DIR = __DIR__ . '/../Entity/';

    private Orm\Factory $factory;
    private ORM\Schema $schema;
    private ORM\ORM $ORM;
    private Database\DatabaseManager $databaseManager;
    private EntityManager $entityManager;

    public function __construct()
    {
        $this->databaseManager = $this->getDbal();
        $this->schema = new ORM\Schema($this->getSchema($this->databaseManager));
        $this->factory = new ORM\Factory($this->databaseManager);
        $this->ORM = new ORM\ORM($this->factory, $this->schema);
        $this->entityManager = new EntityManager($this->ORM);
    }

    /**
     * @return ORM\ORM
     */
    public function getORM(): ORM\ORM
    {
        return $this->ORM;
    }

    public function getEntityManager(): ORM\EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @param Database\DatabaseManager $dbal
     * @return array[]
     */
    protected function getSchema(Database\DatabaseManager $dbal): array
    {
        $classLocator = (new Tokenizer\Tokenizer(
            new Tokenizer\Config\TokenizerConfig(
                [
                    'directories' => [self::ENTITY_DIR],
                ]
            )
        ))->classLocator();

        AnnotationRegistry::registerLoader('class_exists');

        return (new Schema\Compiler())->compile(new Schema\Registry($dbal), [
            new Schema\Generator\ResetTables(),             // re-declared table schemas (remove columns)
//            new Annotated\Embeddings($classLocator),        // register embeddable entities
            new Annotated\Entities($classLocator),          // register annotated entities
//            new Annotated\TableInheritance(),               // register STI/JTI
            new Annotated\MergeColumns(),                   // add @Table column declarations
//            new Schema\Generator\GenerateRelations(),       // generate entity relations
            new Schema\Generator\GenerateModifiers(),       // generate changes from schema modifiers
            new Schema\Generator\ValidateEntities(),        // make sure all entity schemas are correct
            new Schema\Generator\RenderTables(),            // declare table schemas
            new Schema\Generator\RenderRelations(),         // declare relation keys and indexes
            new Schema\Generator\RenderModifiers(),         // render all schema modifiers
            new Annotated\MergeIndexes(),                   // add @Table column declarations
            new Schema\Generator\SyncTables(),              // sync table changes to database
            new Schema\Generator\GenerateTypecast(),        // typecast non string columns
            new Schema\Generator\SyncTables(),              // sync table changes to database
        ]);
    }

    /**
     * @return Database\DatabaseManager
     */
    protected function getDbal(): Database\DatabaseManager
    {
        return new Database\DatabaseManager(
            new Config\DatabaseConfig(
                [
                    'default' => 'default',
                    'databases' => [
                        'default' => ['connection' => 'mysql']
                    ],
                    'connections' => [
                        'mysql' => new Config\MySQLDriverConfig(
                            connection: new Config\MySQL\TcpConnectionConfig(
                                            database: 'place',
                                            host: 'database',
                                            port: 3306,
                                            user:'place',
                                            password: 'place',
                                        ),
                            queryCache: true
                        ),
                    ]
                ]
            )
        );
    }
}
