{extends file="layouts/main.tpl"}

{block name=content}
    <main class="mx-auto max-w-6xl px-4 py-10 sm:px-6 sm:py-12">
        <article class="mx-auto max-w-4xl">
            <header class="article-page__hero rounded-[2rem] px-6 py-10 sm:px-8 sm:py-12">
                <div class="space-y-5">
                    <div class="flex flex-wrap items-center gap-3 text-sm">
                        <a
                            href="/category/{$post.category.slug}"
                            class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 font-semibold text-amber-700"
                        >
                            {$post.category.name}
                        </a>
                        <span class="text-slate-400">{$post.publishedAt}</span>
                        <span class="text-slate-400">{$post.views} views</span>
                        <span class="text-slate-400">{$post.readTime}</span>
                    </div>

                    <h1 class="max-w-3xl text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl">
                        {$post.title}
                    </h1>

                    <p class="max-w-2xl text-lg leading-8 text-slate-600">
                        {$post.description}
                    </p>

                    <div class="flex items-center gap-4 border-t border-slate-200 pt-5">
                        <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-slate-900 text-sm font-bold text-white">
                            {$post.author|substr:0:1}
                        </span>
                        <div>
                            <p class="font-semibold text-slate-900">{$post.author}</p>
                            <p class="text-sm text-slate-500">{$post.authorRole}</p>
                        </div>
                    </div>
                </div>
            </header>

            <div class="mt-8 overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm">
                <img
                    src="{$post.image}"
                    alt="{$post.title} cover"
                    class="h-[22rem] w-full object-cover sm:h-[28rem]"
                >
            </div>

            <div class="article-page__content mt-8 rounded-[2rem] border border-slate-200 bg-white px-6 py-8 shadow-sm sm:px-8 sm:py-10">
                {foreach from=$post.content item=section}
                    <section class="space-y-4">
                        <h2 class="text-2xl font-bold tracking-tight text-slate-900">{$section.heading}</h2>
                        {foreach from=$section.paragraphs item=paragraph}
                            <p class="text-base leading-8 text-slate-600">
                                {$paragraph}
                            </p>
                        {/foreach}
                    </section>
                {/foreach}
            </div>
        </article>

        <section class="mt-14">
            <div class="mb-8 flex flex-col gap-2">
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">Keep reading</p>
                <h2 class="text-3xl font-bold tracking-tight text-slate-900">3 similar articles</h2>
            </div>

            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                {foreach from=$relatedPosts item=post}
                    {include file="partials/related-post-card.tpl" post=$post}
                {/foreach}
            </div>
        </section>
    </main>
{/block}
