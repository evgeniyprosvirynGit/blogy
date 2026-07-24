<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreatePostCategoryTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('post_category', ['id' => false, 'primary_key' => ['post_id', 'category_id']]);

        $table
            ->addColumn('post_id', 'integer', ['signed' => false, 'null' => false])
            ->addColumn('category_id', 'integer', ['signed' => false, 'null' => false])
            ->addForeignKey('post_id', 'posts', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('category_id', 'categories', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addIndex(['post_id'])
            ->addIndex(['category_id'])
            ->addIndex(['category_id', 'post_id'])
            ->create();
    }
}
