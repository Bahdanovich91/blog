<article class="post-card">
    {if $post.image}
        <a href="/post/{$post.slug|escape}" class="post-card__image-wrap">
            <img src="{$post.image|escape}" alt="{$post.title|escape}">
        </a>
    {else}
        <div class="post-card__image-placeholder"></div>
    {/if}

    <div class="post-card__body">
        <h3 class="post-card__title">
            <a href="/post/{$post.slug|escape}">{$post.title|escape}</a>
        </h3>
        <p class="post-card__meta">{$post.created_at_formatted|escape}</p>
        <p class="post-card__excerpt">{$post.description|escape|truncate:160:'...'}</p>
        <a href="/post/{$post.slug|escape}" class="post-card__link">Continue Reading</a>
    </div>
</article>
