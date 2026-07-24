<?php

declare(strict_types=1);

use App\Support\BlogDemoData;
use Phinx\Seed\AbstractSeed;

final class BlogSeeder extends AbstractSeed
{
    public function run(): void
    {
        $this->execute('SET FOREIGN_KEY_CHECKS=0');

        $this->table('post_category')->truncate();
        $this->table('posts')->truncate();
        $this->table('categories')->truncate();

        $this->table('categories')->insert(BlogDemoData::categories())->saveData();
        $this->table('posts')->insert(BlogDemoData::posts())->saveData();
        $this->table('post_category')->insert(BlogDemoData::postCategories())->saveData();

        $this->execute('SET FOREIGN_KEY_CHECKS=1');
    }
}
