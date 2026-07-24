{extends file="layouts/main.tpl"}

{block name=content}
    <main class="home-page mx-auto max-w-6xl px-4 py-10 sm:px-6 sm:py-12">
        <section class="home-page__hero rounded-[2rem] px-6 py-12 sm:px-8 sm:py-14">
            <div class="max-w-3xl space-y-5">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-amber-600">Simple PHP Blog</p>
                <h1 class="max-w-4xl text-5xl font-extrabold tracking-tight text-slate-900 sm:text-6xl">
                    {$pageTitle}
                </h1>
                <p class="max-w-2xl text-lg leading-8 text-slate-600">
                    Curated categories and article previews arranged as a clean editorial archive. Real content will replace these demo entries after seeding.
                </p>
            </div>
        </section>

        {if $categories|@count > 0}
            <section class="mt-12 space-y-12">
                {foreach from=$categories item=category}
                    {include
                        file="partials/category-section.tpl"
                        category=$category
                        posts=$category.posts
                        linkLabel='All posts'
                    }
                {/foreach}
            </section>
        {else}
            <section class="mt-12">
                <div class="rounded-[1.75rem] border border-slate-200 bg-white px-6 py-10 text-center shadow-sm sm:px-8">
                    <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">Homepage status</p>
                    <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900">{$emptyState.title}</h2>
                    <p class="mx-auto mt-4 max-w-2xl text-base leading-8 text-slate-600">
                        {$emptyState.message}
                    </p>
                </div>
            </section>
        {/if}
    </main>
{/block}
