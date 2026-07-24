<nav class="flex justify-center border-t border-slate-200 pt-8" aria-label="Pagination">
    <div class="flex flex-wrap items-center justify-center gap-2">
        <a
            href="{$pagination.prev|default:'#'}"
            class="inline-flex h-10 items-center justify-center rounded-md border border-slate-300 px-3 text-sm font-medium text-slate-400 {if !$pagination.prev}pointer-events-none opacity-50{else}hover:border-slate-900 hover:bg-slate-900 hover:text-white{/if}"
        >
            Previous
        </a>
        {foreach from=$pagination.pages item=page}
            <a
                href="{$page.href}"
                class="inline-flex h-10 min-w-10 items-center justify-center rounded-md border px-3 text-sm font-medium transition {if $page.active}border-slate-900 bg-slate-900 text-white{else}border-slate-300 text-slate-700 hover:border-slate-900 hover:bg-slate-900 hover:text-white{/if}"
                aria-current="{if $page.active}page{else}false{/if}"
            >
                {$page.label}
            </a>
        {/foreach}
        <a
            href="{$pagination.next|default:'#'}"
            class="inline-flex h-10 items-center justify-center rounded-md border border-slate-300 px-3 text-sm font-medium text-slate-700 {if !$pagination.next}pointer-events-none opacity-50{else}hover:border-slate-900 hover:bg-slate-900 hover:text-white{/if}"
        >
            Next
        </a>
    </div>
</nav>
