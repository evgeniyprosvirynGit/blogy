<a
    href="{$post.href}"
    class="category-post-card group"
>
    <div class="category-post-card__media">
        <picture>
            {if $post.image.webp_srcset}
                <source srcset="{$post.image.webp_srcset}" sizes="{$post.image.sizes}" type="image/webp">
            {/if}
            <img
                src="{$post.image.src}"
                {if $post.image.srcset}srcset="{$post.image.srcset}"{/if}
                sizes="{$post.image.sizes}"
                alt="{$post.title} cover"
                class="category-post-card__image"
                loading="lazy"
                decoding="async"
            >
        </picture>
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
