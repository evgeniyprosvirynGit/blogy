<article class="space-y-6 rounded-[1.75rem] border border-slate-200 bg-white px-5 py-6 shadow-sm sm:px-6 sm:py-7">
    <div class="flex flex-col gap-5 border-b border-slate-200 pb-5 md:flex-row md:items-end md:justify-between">
        <div class="space-y-2">
            {if isset($eyebrow) && $eyebrow}
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">{$eyebrow}</p>
            {/if}
            <h2 class="text-3xl font-bold tracking-tight text-slate-900">{$category.name}</h2>
            {if isset($category.description) && $category.description}
                <p class="max-w-2xl text-sm leading-7 text-slate-600">{$category.description}</p>
            {/if}
        </div>
        <a
            href="/category/{$category.slug}"
            class="inline-flex h-12 items-center justify-center rounded-full border border-slate-300 px-5 text-sm font-semibold text-slate-900 transition hover:border-slate-900 hover:bg-slate-900 hover:text-white"
        >
            {$linkLabel|default:'Посмотреть все'}
        </a>
    </div>

    <div class="grid gap-6 md:grid-cols-3">
        {foreach from=$posts item=post}
            {include
                file="partials/post-card.tpl"
                href=$post.href
                image=$post.image
                title=$post.title
                meta=$post.meta
                description=$post.description
            }
        {/foreach}
    </div>
</article>
