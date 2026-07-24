<?php

declare(strict_types=1);

use App\Classes\Errors\FileErrorLogger;
use App\Classes\Posts\RelatedArticlesProvider;
use App\Classes\Errors\TemplateErrorHandler;
use App\Core\Database;
use App\Core\View;
use App\Support\BlogDemoData;
use Illuminate\Database\Capsule\Manager as Capsule;

function testView(): View
{
    $config = testAppConfig();
    $tmpBase = sys_get_temp_dir() . '/blogy-tests-smarty';
    $compilePath = $tmpBase . '/compile';
    $cachePath = $tmpBase . '/cache';

    if (! is_dir($compilePath)) {
        mkdir($compilePath, 0777, true);
    }

    if (! is_dir($cachePath)) {
        mkdir($cachePath, 0777, true);
    }

    $config['paths']['smarty']['compile'] = $compilePath;
    $config['paths']['smarty']['cache'] = $cachePath;

    return new View($config);
}

function testAppConfig(): array
{
    return require dirname(__DIR__) . '/config/app.php';
}

function testErrorHandler(): TemplateErrorHandler
{
    return new TemplateErrorHandler(
        testView(),
        new FileErrorLogger(
            sys_get_temp_dir() . '/blogy-tests-error.log',
            sys_get_temp_dir() . '/blogy-tests-application.log',
        ),
    );
}

function testRelatedArticlesProvider(): RelatedArticlesProvider
{
    return new RelatedArticlesProvider(testAppConfig()['blog']['article_page']['related_posts_limit']);
}

function testDatabase(): Capsule
{
    Database::reset();

    $databasePath = sys_get_temp_dir() . '/blogy-tests.sqlite';

    if (is_file($databasePath)) {
        unlink($databasePath);
    }

    touch($databasePath);

    $capsule = Database::boot([
        'driver' => 'sqlite',
        'database' => $databasePath,
        'prefix' => '',
    ]);

    $schema = $capsule->schema();

    $schema->create('categories', static function ($table): void {
        $table->increments('id');
        $table->string('name');
        $table->string('slug')->unique();
        $table->text('description')->nullable();
        $table->timestamps();
    });

    $schema->create('posts', static function ($table): void {
        $table->increments('id');
        $table->string('image')->nullable();
        $table->string('title');
        $table->string('slug')->unique();
        $table->text('description')->nullable();
        $table->text('content');
        $table->unsignedInteger('views')->default(0);
        $table->dateTime('published_at')->nullable();
        $table->timestamps();
    });

    $schema->create('post_category', static function ($table): void {
        $table->unsignedInteger('post_id');
        $table->unsignedInteger('category_id');
        $table->primary(['post_id', 'category_id']);
    });

    $capsule->table('categories')->insert(BlogDemoData::categories());
    $capsule->table('posts')->insert(BlogDemoData::posts());
    $capsule->table('post_category')->insert(BlogDemoData::postCategories());

    return $capsule;
}
