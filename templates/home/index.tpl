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
                    <article class="space-y-6">
                        <div class="flex flex-col gap-4 border-b border-slate-200 pb-4 md:flex-row md:items-end md:justify-between">
                            <div class="space-y-2">
                                <h2 class="text-3xl font-bold text-slate-900">{$category.name}</h2>
                                <p class="max-w-2xl text-sm leading-7 text-slate-600">{$category.description}</p>
                            </div>
                            <a
                                href="/category/{$category.slug}"
                                class="inline-flex items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-700"
                            >
                                All posts
                            </a>
                        </div>

                        <div class="grid gap-6 md:grid-cols-3">
                            {foreach from=$category.posts item=post}
                                <article class="rounded-[1.5rem] border border-slate-200 bg-white p-6 shadow-sm">
                                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">
                                        {$post.published_at|date_format:"%b %e, %Y"}
                                    </p>
                                    <h3 class="mt-4 text-xl font-bold text-slate-900">{$post.title}</h3>
                                    <p class="mt-3 text-sm leading-7 text-slate-600">{$post.description}</p>
                                    <a
                                        href="/post/{$post.slug}"
                                        class="mt-5 inline-flex text-sm font-semibold text-amber-700 hover:text-amber-800"
                                    >
                                        Read article
                                    </a>
                                </article>
                            {/foreach}
                        </div>
                    </article>
                {/foreach}
            </section>
        {else}
            <section class="mt-12 rounded-[2rem] border border-dashed border-slate-300 bg-white p-10 text-center shadow-sm">
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-400">No content yet</p>
                <h2 class="mt-4 text-3xl font-bold text-slate-900">Database is ready for blog data</h2>
                <p class="mx-auto mt-3 max-w-2xl text-sm leading-7 text-slate-600">
                    Run the schema and seed data, then the homepage will show categories with the latest posts automatically.
                </p>
            </section>
        {/if}
    </main>
{/block}
