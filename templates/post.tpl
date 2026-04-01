{extends file="layout.tpl"}

{block name="title"}{$post.title|escape} &mdash; Blogy{/block}

{block name="content"}
<div class="post-page">
    <div class="container">

        {if $post.image}
            <div class="post-hero">
                <img src="{$post.image|escape}" alt="{$post.title|escape}">
            </div>
        {/if}

        <div class="post-header">
            <h1 class="post-header__title">{$post.title|escape}</h1>
            <div class="post-meta">
                <span>{$post.created_at_formatted|escape}</span>
                <span>{$post.view_count} views</span>
                {if $post.categories}
                    <span>
                        {foreach $post.categories as $cat}
                            <a href="/category/{$cat.slug|escape}">{$cat.name|escape}</a>{if !$cat@last}, {/if}
                        {/foreach}
                    </span>
                {/if}
            </div>
        </div>

        {if $post.description}
            <p class="post-description">{$post.description|escape}</p>
        {/if}

        <div class="post-content">
            {$post_content_html nofilter}
        </div>

        {if $similar_posts}
            <div class="similar-posts">
                <h2 class="similar-posts__title">Similar Posts</h2>
                <div class="cards-grid">
                    {foreach $similar_posts as $post}
                        {include file="partials/post_card.tpl" post=$post}
                    {/foreach}
                </div>
            </div>
        {/if}

    </div>
</div>
{/block}
