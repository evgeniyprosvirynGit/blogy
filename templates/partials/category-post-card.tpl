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
        <div class="flex flex-wrap gap-2">
            {foreach from=$post.badges item=badge}
                <span class="category-post-card__badge">
                    {$badge}
                </span>
            {/foreach}
        </div>

        <h2 class="category-post-card__title">
            {$post.title}
        </h2>

        <p class="category-post-card__description">
            {$post.description}
        </p>

        <div class="category-post-card__meta">
            <span class="category-post-card__avatar">
                {$post.author|substr:0:1}
            </span>
            <div class="flex items-center gap-2">
                <span class="font-semibold text-slate-700">{$post.author}</span>
                <span class="text-slate-300">•</span>
                <span>{$post.date}</span>
            </div>
        </div>
    </div>
</a>
