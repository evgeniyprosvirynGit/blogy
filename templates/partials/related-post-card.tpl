<a
    href="{$post.href}"
    class="group block overflow-hidden rounded-[1.5rem] border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
>
    <article class="flex h-full flex-col">
        <img
            src="{$post.image}"
            alt="{$post.title} cover"
            class="h-44 w-full object-cover"
        >
        <div class="flex flex-1 flex-col p-5">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">{$post.meta}</p>
            <h3 class="mt-3 text-xl font-bold leading-8 text-slate-900 transition group-hover:text-amber-700">
                {$post.title}
            </h3>
            <p class="mt-3 flex-1 text-sm leading-7 text-slate-600">
                {$post.description}
            </p>
            <span class="mt-5 inline-flex text-sm font-semibold text-amber-700 transition group-hover:text-amber-800">
                Read article
            </span>
        </div>
    </article>
</a>
