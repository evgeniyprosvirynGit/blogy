<a
    href="{$post.href}"
    class="category-post-card group"
>
    <div class="category-post-card__media">
        <img
            src="{$post.image}"
            alt="{$post.title} cover"
            class="category-post-card__image"
        >
    </div>

    <div class="category-post-card__body">
        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">{$post.meta}</p>

        <h2 class="category-post-card__title">
            {$post.title}
        </h2>

        <p class="category-post-card__description">
            {$post.description}
        </p>
    </div>
</a>
