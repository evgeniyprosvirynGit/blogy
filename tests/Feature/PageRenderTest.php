<?php

declare(strict_types=1);

use App\Controllers\CategoryController;
use App\Controllers\HomeController;
use App\Controllers\PostController;

beforeEach(function (): void {
    testDatabase();
});

it('renders the homepage with seeded categories and posts', function (): void {
    $config = testAppConfig();
    $controller = new HomeController(
        testView(),
        testErrorHandler(),
        testResponsiveImageService(),
        $config['blog']['homepage']['page_title'],
        $config['blog']['homepage']['categories_limit'],
        $config['blog']['homepage']['posts_per_category'],
        $config['blog']['homepage']['default_post_image'],
    );

    $html = $controller->index();

    expect($html)
        ->toContain('Simple PHP Blog')
        ->toContain('Design Systems')
        ->toContain('Frontend Engineering')
        ->toContain('/category/design-systems')
        ->toContain('/post/building-a-category-page');
});

it('renders only categories that have posts on the homepage', function (): void {
    \App\Models\Category::query()->create([
        'name' => 'Empty Category',
        'slug' => 'empty-category',
        'description' => 'Should stay hidden on the homepage.',
    ]);

    $config = testAppConfig();
    $controller = new HomeController(
        testView(),
        testErrorHandler(),
        testResponsiveImageService(),
        $config['blog']['homepage']['page_title'],
        10,
        $config['blog']['homepage']['posts_per_category'],
        $config['blog']['homepage']['default_post_image'],
    );

    $html = $controller->index();

    expect($html)
        ->toContain('Design Systems')
        ->not->toContain('Empty Category');
});

it('renders exactly three latest posts per category on the homepage', function (): void {
    $config = testAppConfig();
    $controller = new HomeController(
        testView(),
        testErrorHandler(),
        testResponsiveImageService(),
        $config['blog']['homepage']['page_title'],
        $config['blog']['homepage']['categories_limit'],
        $config['blog']['homepage']['posts_per_category'],
        $config['blog']['homepage']['default_post_image'],
    );

    $html = $controller->index();

    expect($html)
        ->toContain('/post/building-a-category-page')
        ->toContain('/post/editorial-ux-patterns')
        ->toContain('/post/meaningful-card-layouts')
        ->and(substr_count($html, '/post/sort-controls'))->toBe(1);
});

it('renders all available posts when a homepage category has fewer than three articles', function (): void {
    $category = \App\Models\Category::query()->create([
        'name' => 'Architecture Notes',
        'slug' => 'architecture-notes',
        'description' => 'A tiny category with two posts.',
    ]);

    $firstPost = \App\Models\Post::query()->create([
        'title' => 'Architecture draft one',
        'slug' => 'architecture-draft-one',
        'image' => '/images/blog.jpg',
        'description' => 'Draft one.',
        'content' => 'Draft one body.',
        'views' => 10,
        'published_at' => '2026-07-24 08:00:00',
    ]);

    $secondPost = \App\Models\Post::query()->create([
        'title' => 'Architecture draft two',
        'slug' => 'architecture-draft-two',
        'image' => '/images/blog.jpg',
        'description' => 'Draft two.',
        'content' => 'Draft two body.',
        'views' => 15,
        'published_at' => '2026-07-24 09:00:00',
    ]);

    $firstPost->categories()->attach($category->id);
    $secondPost->categories()->attach($category->id);

    $config = testAppConfig();
    $controller = new HomeController(
        testView(),
        testErrorHandler(),
        testResponsiveImageService(),
        $config['blog']['homepage']['page_title'],
        10,
        $config['blog']['homepage']['posts_per_category'],
        $config['blog']['homepage']['default_post_image'],
    );

    $html = $controller->index();

    expect($html)
        ->toContain('Architecture Notes')
        ->toContain('/post/architecture-draft-one')
        ->toContain('/post/architecture-draft-two');
});

it('renders correct all posts links for homepage categories', function (): void {
    $config = testAppConfig();
    $controller = new HomeController(
        testView(),
        testErrorHandler(),
        testResponsiveImageService(),
        $config['blog']['homepage']['page_title'],
        $config['blog']['homepage']['categories_limit'],
        $config['blog']['homepage']['posts_per_category'],
        $config['blog']['homepage']['default_post_image'],
    );

    $html = $controller->index();

    expect($html)
        ->toContain('/category/design-systems')
        ->toContain('/category/frontend-engineering')
        ->toContain('/category/content-strategy')
        ->toContain('/category/product-thinking');
});

it('renders an empty state on homepage when there are no categories', function (): void {
    \App\Models\Post::query()->delete();
    \App\Models\Category::query()->delete();

    $config = testAppConfig();
    $controller = new HomeController(
        testView(),
        testErrorHandler(),
        testResponsiveImageService(),
        $config['blog']['homepage']['page_title'],
        $config['blog']['homepage']['categories_limit'],
        $config['blog']['homepage']['posts_per_category'],
        $config['blog']['homepage']['default_post_image'],
    );

    $html = $controller->index();

    expect($html)
        ->toContain('No categories published yet')
        ->toContain('The homepage is connected to the database, but no categories are available for display yet.');
});

it('renders the category page with sorting controls and article cards', function (): void {
    $config = testAppConfig();
    $controller = new CategoryController(
        testView(),
        testErrorHandler(),
        testResponsiveImageService(),
        $config['blog']['homepage']['default_post_image'],
        $config['blog']['category_page']['posts_per_page'],
        testCategoryPostSorter(),
        testCategoryPaginator(),
    );

    $html = $controller->show('design-systems');

    expect($html)
        ->toContain('Design Systems')
        ->toContain('4 articles')
        ->toContain('Newest first')
        ->toContain('Oldest first')
        ->toContain('Most viewed')
        ->toContain('Least viewed')
        ->toContain('Building a category page that scales with editorial content')
        ->toContain('Jul 21, 2026')
        ->toContain('/post/editorial-ux-patterns');
});

it('falls back to default category sorting when sort parameter is missing or invalid', function (): void {
    $config = testAppConfig();

    $controller = new CategoryController(
        testView(),
        testErrorHandler(),
        testResponsiveImageService(),
        $config['blog']['homepage']['default_post_image'],
        12,
        testCategoryPostSorter(),
        testCategoryPaginator(),
    );

    $defaultHtml = $controller->show('design-systems');
    $_GET['sort'] = 'views desc; drop table posts';
    $invalidHtml = $controller->show('design-systems');
    unset($_GET['sort']);

    expect($defaultHtml)->toContain('/post/building-a-category-page')
        ->and($invalidHtml)->toContain('/post/building-a-category-page')
        ->and($invalidHtml)->toContain('Newest first');
});

it('renders real pagination links on category pages', function (): void {
    $_GET['page'] = '2';
    $_GET['sort'] = 'published_at';

    $controller = new CategoryController(
        testView(),
        testErrorHandler(),
        testResponsiveImageService(),
        testAppConfig()['blog']['homepage']['default_post_image'],
        2,
        testCategoryPostSorter(),
        testCategoryPaginator(),
    );

    $html = $controller->show('design-systems');

    unset($_GET['page'], $_GET['sort']);

    expect($html)
        ->toContain('/category/design-systems?sort=published_at_desc&amp;page=1')
        ->toContain('/category/design-systems?sort=published_at_desc&amp;page=2')
        ->toContain('/post/meaningful-card-layouts')
        ->toContain('/post/sort-controls');
});

it('clamps out of range category pages to the last available page content', function (): void {
    $_GET['page'] = '999';
    $_GET['sort'] = 'published_at';

    $controller = new CategoryController(
        testView(),
        testErrorHandler(),
        testResponsiveImageService(),
        testAppConfig()['blog']['homepage']['default_post_image'],
        2,
        testCategoryPostSorter(),
        testCategoryPaginator(),
    );

    $html = $controller->show('design-systems');

    unset($_GET['page'], $_GET['sort']);

    expect($html)
        ->toContain('/category/design-systems?sort=published_at_desc&amp;page=1')
        ->toContain('/category/design-systems?sort=published_at_desc&amp;page=2')
        ->toContain('/post/meaningful-card-layouts')
        ->toContain('/post/sort-controls')
        ->not->toContain('/post/building-a-category-page');
});

it('normalizes invalid category page values to the first page', function (string $page): void {
    $_GET['page'] = $page;
    $_GET['sort'] = 'published_at';

    $controller = new CategoryController(
        testView(),
        testErrorHandler(),
        testResponsiveImageService(),
        testAppConfig()['blog']['homepage']['default_post_image'],
        2,
        testCategoryPostSorter(),
        testCategoryPaginator(),
    );

    $html = $controller->show('design-systems');

    unset($_GET['page'], $_GET['sort']);

    expect($html)
        ->toContain('/post/building-a-category-page')
        ->toContain('/post/editorial-ux-patterns')
        ->not->toContain('/post/meaningful-card-layouts');
})->with([
    'negative page' => '-1',
    'zero page' => '0',
    'string page' => 'abc',
]);

it('preserves the selected sort across pagination links', function (): void {
    $_GET['page'] = '2';
    $_GET['sort'] = 'views_desc';

    $controller = new CategoryController(
        testView(),
        testErrorHandler(),
        testResponsiveImageService(),
        testAppConfig()['blog']['homepage']['default_post_image'],
        2,
        testCategoryPostSorter(),
        testCategoryPaginator(),
    );

    $html = $controller->show('design-systems');

    unset($_GET['page'], $_GET['sort']);

    expect($html)
        ->toContain('/category/design-systems?sort=views_desc&amp;page=1')
        ->toContain('/category/design-systems?sort=views_desc&amp;page=2');
});

it('renders category posts sorted by oldest publication date first', function (): void {
    $_GET['sort'] = 'published_at_asc';

    $controller = new CategoryController(
        testView(),
        testErrorHandler(),
        testResponsiveImageService(),
        testAppConfig()['blog']['homepage']['default_post_image'],
        testAppConfig()['blog']['category_page']['posts_per_page'],
        testCategoryPostSorter(),
        testCategoryPaginator(),
    );

    $html = $controller->show('design-systems');

    unset($_GET['sort']);

    expect(strpos($html, '/post/sort-controls'))->toBeLessThan(strpos($html, '/post/building-a-category-page'));
});

it('renders category posts sorted by least views first', function (): void {
    $_GET['sort'] = 'views_asc';

    $controller = new CategoryController(
        testView(),
        testErrorHandler(),
        testResponsiveImageService(),
        testAppConfig()['blog']['homepage']['default_post_image'],
        testAppConfig()['blog']['category_page']['posts_per_page'],
        testCategoryPostSorter(),
        testCategoryPaginator(),
    );

    $html = $controller->show('design-systems');

    unset($_GET['sort']);

    expect(strpos($html, '/post/sort-controls'))->toBeLessThan(strpos($html, '/post/building-a-category-page'));
});

it('renders a last pagination page with fewer items when the page is incomplete', function (): void {
    $_GET['page'] = '2';
    $_GET['sort'] = 'published_at';

    $controller = new CategoryController(
        testView(),
        testErrorHandler(),
        testResponsiveImageService(),
        testAppConfig()['blog']['homepage']['default_post_image'],
        3,
        testCategoryPostSorter(),
        testCategoryPaginator(),
    );

    $html = $controller->show('design-systems');

    unset($_GET['page'], $_GET['sort']);

    expect($html)
        ->toContain('/post/sort-controls')
        ->not->toContain('/post/editorial-ux-patterns')
        ->not->toContain('/post/meaningful-card-layouts');
});

it('renders the article page with full content and related articles', function (): void {
    $controller = new PostController(
        testView(),
        testErrorHandler(),
        testResponsiveImageService(),
        testRelatedArticlesProvider(),
        testAppConfig()['blog']['homepage']['default_post_image'],
    );

    $html = $controller->show('building-a-category-page');

    expect($html)
        ->toContain('Building a category page that scales with editorial content')
        ->toContain('/category/design-systems')
        ->toContain('src="/images/cache/article_cover/')
        ->toContain('July 21, 2026')
        ->toContain('The article page is where the visual language of the blog either holds together or falls apart.')
        ->toContain('3 similar articles')
        ->toContain('2,431 views')
        ->toContain('Editorial UX patterns for article archives');
});

it('escapes article content and category data in rendered templates', function (): void {
    \App\Models\Category::query()->where('id', 1)->update([
        'name' => '<script>alert("category")</script> Дизайн',
    ]);

    \App\Models\Post::query()->where('slug', 'building-a-category-page')->update([
        'title' => '<script>alert("title")</script> Заголовок',
        'description' => '<b>Описание</b> и кириллица',
        'content' => "<script>alert('content')</script>\n\nАбзац с кириллицей",
    ]);

    $controller = new PostController(
        testView(),
        testErrorHandler(),
        testResponsiveImageService(),
        testRelatedArticlesProvider(),
        testAppConfig()['blog']['homepage']['default_post_image'],
    );
    $html = $controller->show('building-a-category-page');

    expect($html)
        ->toContain('&lt;script&gt;alert(&quot;title&quot;)&lt;/script&gt; Заголовок')
        ->toContain('&lt;b&gt;Описание&lt;/b&gt; и кириллица')
        ->toContain('&lt;script&gt;alert(&#039;content&#039;)&lt;/script&gt;')
        ->toContain('&lt;script&gt;alert(&quot;category&quot;)&lt;/script&gt; Дизайн');
});

it('uses the default image on article and category pages when a post image is empty', function (): void {
    \App\Models\Post::query()->where('slug', 'building-a-category-page')->update([
        'image' => '',
    ]);

    $categoryController = new CategoryController(
        testView(),
        testErrorHandler(),
        testResponsiveImageService(),
        testAppConfig()['blog']['homepage']['default_post_image'],
        testAppConfig()['blog']['category_page']['posts_per_page'],
        testCategoryPostSorter(),
        testCategoryPaginator(),
    );
    $postController = new PostController(
        testView(),
        testErrorHandler(),
        testResponsiveImageService(),
        testRelatedArticlesProvider(),
        testAppConfig()['blog']['homepage']['default_post_image'],
    );

    $categoryHtml = $categoryController->show('design-systems');
    $postHtml = $postController->show('building-a-category-page');

    expect($categoryHtml)->toContain('/images/cache/category_card/')
        ->and($postHtml)->toContain('/images/cache/article_cover/');
});

it('renders a not found page when category does not exist', function (): void {
    $config = testAppConfig();
    $controller = new CategoryController(
        testView(),
        testErrorHandler(),
        testResponsiveImageService(),
        $config['blog']['homepage']['default_post_image'],
        $config['blog']['category_page']['posts_per_page'],
        testCategoryPostSorter(),
        testCategoryPaginator(),
    );

    $html = $controller->show('missing-category');

    expect($html)
        ->toContain('Category not found')
        ->toContain('The requested category does not exist or has been removed.');
});

it('renders a not found page when article does not exist', function (): void {
    $controller = new PostController(
        testView(),
        testErrorHandler(),
        testResponsiveImageService(),
        testRelatedArticlesProvider(),
        testAppConfig()['blog']['homepage']['default_post_image'],
    );

    $html = $controller->show('missing-article');

    expect($html)
        ->toContain('Article not found')
        ->toContain('The requested article does not exist or has been removed.');
});
