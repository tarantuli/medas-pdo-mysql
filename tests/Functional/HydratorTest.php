<?php

declare(strict_types=1);

namespace Medas\PdoMysqlTest\Functional;

use Medas\EntityManager\EntityManager;

use Medas\PdoMysqlTest\MockUps\Migrations\StoredEntity;
use function service;

class HydratorTest extends BaseTestClass
{
    public function testHydrateEntity(): void
    {
        $entityManager = service(EntityManager::class);

        $entity = $entityManager->get(StoredEntity::class, 1);

        self::assertInstanceOf(StoredEntity::class, $entity);
        self::assertEquals(1, $entity->id());
        self::assertNull($entity->createdAt);
    }
}
