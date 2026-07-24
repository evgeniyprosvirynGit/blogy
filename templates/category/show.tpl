{extends file="layouts/main.tpl"}

{block name=content}
    <main class="category-page mx-auto max-w-6xl px-4 py-10 sm:px-6 sm:py-12">
        <section class="category-page__hero overflow-hidden rounded-[2rem] px-6 py-14 sm:px-8 lg:px-12">
            <div class="mx-auto max-w-3xl space-y-4 text-center">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-amber-600">Blog category</p>
                <h1 class="text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl">{$category.name}</h1>
                <p class="mx-auto max-w-2xl text-sm leading-8 text-slate-600 sm:text-base">
                    {$category.description}
                </p>
            </div>
        </section>

        <section class="mt-10 space-y-8">
            {include file="partials/sort-controls.tpl" sortOptions=$sortOptions currentSort=$currentSort}

            <div class="category-page__grid">
                {foreach from=$posts item=post}
                    {include file="partials/category-post-card.tpl" post=$post}
                {/foreach}
            </div>

            {include file="partials/pagination.tpl" pagination=$pagination}
        </section>
    </main>
{/block}
