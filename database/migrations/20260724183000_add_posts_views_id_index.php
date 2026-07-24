<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddPostsViewsIdIndex extends AbstractMigration
{
    public function change(): void
    {
        $this->table('posts')
            ->addIndex(['views', 'id'])
            ->update();
    }
}
