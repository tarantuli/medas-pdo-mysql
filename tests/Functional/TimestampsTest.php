<?php

declare(strict_types=1);

namespace Medas\PdoMysqlTest\Functional;

use Medas\PdoStorage\Table;

use Medas\PdoMysqlTest\MockUps\Attributes\TimestampedPost;

class TimestampsTest extends BaseTestClass
{
    private const TABLE_NAME = 'timestamped_posts';

    public function testCreateTable(): void
    {
        $this->controller()->deleteStore($this->store(self::TABLE_NAME));
        $migration = $this->createMigrationClassContent('Attributes');
        $this->executeMigration($migration);

        self::assertInstanceOf(Table::class, $this->store(self::TABLE_NAME));
    }

    /**
     * @depends testCreateTable
     */
    public function testCreateInstance(): TimestampedPost
    {
        $post = new TimestampedPost();

        em()->persist($post);
        em()->flush();

        self::assertInstanceOf(\DateTime::class, $post->createdAt());

        return $post;
    }

    /**
     * @depends testCreateInstance
     */
    public function testUpdateInstance(TimestampedPost $post): void
    {
        $post->counter++;

        em()->flush();

        self::assertNotEquals(
            $post->createdAt()->format(\DateTimeInterface::RFC3339_EXTENDED),
            $post->modifiedAt()->format(\DateTimeInterface::RFC3339_EXTENDED)
        );
    }
}
