<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreatePostsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('posts');

        $table
            ->addColumn('image', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('title', 'string', ['limit' => 255])
            ->addColumn('slug', 'string', ['limit' => 255])
            ->addColumn('description', 'text', ['null' => true])
            ->addColumn('content', 'text')
            ->addColumn('views', 'integer', ['default' => 0, 'signed' => false])
            ->addColumn('published_at', 'datetime', ['null' => true])
            ->addTimestamps()
            ->addIndex(['title'])
            ->addIndex(['slug'], ['unique' => true])
            ->addIndex(['views'])
            ->addIndex(['published_at'])
            ->addIndex(['published_at', 'id'])
            ->create();
    }
}
