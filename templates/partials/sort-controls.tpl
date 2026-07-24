<div class="category-sort-bar flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
    <div class="space-y-1">
        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Sort articles</p>
        <p class="text-sm text-slate-500">Choose how the article list is ordered.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        {foreach from=$sortOptions item=option}
            <a
                href="{$option.href}"
                class="inline-flex h-10 items-center justify-center rounded-full border px-4 text-sm font-semibold transition {if $currentSort === $option.value}border-amber-500 bg-amber-500 text-white{else}border-slate-300 bg-white text-slate-700 hover:border-slate-900 hover:bg-slate-900 hover:text-white{/if}"
            >
                {$option.label}
            </a>
        {/foreach}
    </div>
</div>
