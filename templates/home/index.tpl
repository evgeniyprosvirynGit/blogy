{extends file="layouts/main.tpl"}

{block name=content}
    <main class="mx-auto max-w-6xl px-6 py-16">
        <section class="space-y-6 border-b border-slate-200 pb-10">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-amber-600">Simple PHP Blog</p>
            <h1 class="max-w-4xl text-5xl font-extrabold tracking-tight text-slate-900 sm:text-6xl">
                {$pageTitle}
            </h1>
            <p class="max-w-2xl text-lg leading-8 text-slate-600">
                Blog homepage bootstrap is ready. Categories and latest posts will appear here as soon as the database is seeded.
            </p>
        </section>

        {if $categories|@count > 0}
            <section class="mt-12 space-y-12">
                {foreach from=$categories item=category}
                    {assign var=categoryPosts value=[]}
                    {foreach from=$category.posts item=post}
                        {$categoryPosts[]=[
                            'href' => "/post/{$post.slug}",
                            'image' => $post.image|default:'/images/blog.jpg',
                            'title' => $post.title,
                            'meta' => $post.published_at|date_format:"%b %e, %Y",
                            'description' => $post.description
                        ]}
                    {/foreach}

                    {include
                        file="partials/category-section.tpl"
                        category=$category
                        posts=$categoryPosts
                        linkLabel='All posts'
                    }
                {/foreach}
            </section>
        {else}
            <section class="mt-12 space-y-12">
                {assign var=demoCategories value=[
                    [
                        'name' => 'Category 1',
                        'slug' => 'category-1',
                        'posts' => [
                            [
                                'href' => '/post/category-1-article-1',
                                'image' => '/images/blog.jpg',
                                'title' => 'Category 1 Article 1',
                                'meta' => 'Demo Post 1',
                                'description' => 'Temporary homepage card layout for the future category feed. Real posts will replace these placeholders after seeding.'
                            ],
                            [
                                'href' => '/post/category-1-article-2',
                                'image' => '/images/blogs.jpg',
                                'title' => 'Category 1 Article 2',
                                'meta' => 'Demo Post 2',
                                'description' => 'Temporary homepage card layout for the future category feed. Real posts will replace these placeholders after seeding.'
                            ],
                            [
                                'href' => '/post/category-1-article-3',
                                'image' => '/images/images.jpeg',
                                'title' => 'Category 1 Article 3',
                                'meta' => 'Demo Post 3',
                                'description' => 'Temporary homepage card layout for the future category feed. Real posts will replace these placeholders after seeding.'
                            ]
                        ]
                    ],
                    [
                        'name' => 'Category 2',
                        'slug' => 'category-2',
                        'posts' => [
                            [
                                'href' => '/post/category-2-article-1',
                                'image' => '/images/blogs.jpg',
                                'title' => 'Category 2 Article 1',
                                'meta' => 'Demo Post 1',
                                'description' => 'Temporary homepage card layout for the future category feed. Real posts will replace these placeholders after seeding.'
                            ],
                            [
                                'href' => '/post/category-2-article-2',
                                'image' => '/images/images.jpeg',
                                'title' => 'Category 2 Article 2',
                                'meta' => 'Demo Post 2',
                                'description' => 'Temporary homepage card layout for the future category feed. Real posts will replace these placeholders after seeding.'
                            ],
                            [
                                'href' => '/post/category-2-article-3',
                                'image' => '/images/blog.jpg',
                                'title' => 'Category 2 Article 3',
                                'meta' => 'Demo Post 3',
                                'description' => 'Temporary homepage card layout for the future category feed. Real posts will replace these placeholders after seeding.'
                            ]
                        ]
                    ],
                    [
                        'name' => 'Category 3',
                        'slug' => 'category-3',
                        'posts' => [
                            [
                                'href' => '/post/category-3-article-1',
                                'image' => '/images/images.jpeg',
                                'title' => 'Category 3 Article 1',
                                'meta' => 'Demo Post 1',
                                'description' => 'Temporary homepage card layout for the future category feed. Real posts will replace these placeholders after seeding.'
                            ],
                            [
                                'href' => '/post/category-3-article-2',
                                'image' => '/images/blog.jpg',
                                'title' => 'Category 3 Article 2',
                                'meta' => 'Demo Post 2',
                                'description' => 'Temporary homepage card layout for the future category feed. Real posts will replace these placeholders after seeding.'
                            ],
                            [
                                'href' => '/post/category-3-article-3',
                                'image' => '/images/blogs.jpg',
                                'title' => 'Category 3 Article 3',
                                'meta' => 'Demo Post 3',
                                'description' => 'Temporary homepage card layout for the future category feed. Real posts will replace these placeholders after seeding.'
                            ]
                        ]
                    ],
                    [
                        'name' => 'Category 4',
                        'slug' => 'category-4',
                        'posts' => [
                            [
                                'href' => '/post/category-4-article-1',
                                'image' => '/images/blog.jpg',
                                'title' => 'Category 4 Article 1',
                                'meta' => 'Demo Post 1',
                                'description' => 'Temporary homepage card layout for the future category feed. Real posts will replace these placeholders after seeding.'
                            ],
                            [
                                'href' => '/post/category-4-article-2',
                                'image' => '/images/blogs.jpg',
                                'title' => 'Category 4 Article 2',
                                'meta' => 'Demo Post 2',
                                'description' => 'Temporary homepage card layout for the future category feed. Real posts will replace these placeholders after seeding.'
                            ],
                            [
                                'href' => '/post/category-4-article-3',
                                'image' => '/images/images.jpeg',
                                'title' => 'Category 4 Article 3',
                                'meta' => 'Demo Post 3',
                                'description' => 'Temporary homepage card layout for the future category feed. Real posts will replace these placeholders after seeding.'
                            ]
                        ]
                    ]
                ]}

                {foreach from=$demoCategories item=category}
                    {include
                        file="partials/category-section.tpl"
                        category=$category
                        posts=$category.posts
                        eyebrow='Category'
                        linkLabel='Посмотреть все'
                    }
                {/foreach}
            </section>
        {/if}
    </main>
{/block}
