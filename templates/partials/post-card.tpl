<a
    href="{$href}"
    class="group block cursor-pointer overflow-hidden rounded-[1.5rem] border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
>
    <article class="flex h-full flex-col">
        <img
            src="{$image}"
            alt="{$title} cover"
            class="h-48 w-full rounded-t-[1.5rem] object-cover"
        >
        <div class="p-6">
            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">
                {$meta}
            </p>
            <h3 class="mt-4 text-xl font-bold text-slate-900 transition group-hover:text-amber-700">
                {$title}
            </h3>
            <p class="mt-3 text-sm leading-7 text-slate-600">
                {$description}
            </p>
            <span class="mt-5 inline-flex text-sm font-semibold text-amber-700 transition group-hover:text-amber-800">
                Read article
            </span>
        </div>
    </article>
</a>
