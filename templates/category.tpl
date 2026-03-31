{extends file="layout.tpl"}

{block name="title"}{$category.name|escape} &mdash; Blogy{/block}

{block name="content"}
<div class="category-page">
    <div class="container">
        <h1 class="category-page__title">{$category.name|escape}</h1>
        {if $category.description}
            <p class="category-page__description">{$category.description|escape}</p>
        {/if}

        <div class="sort-bar">
            <span>Sort by:</span>
            <a href="{$base_url}/category/{$category.slug|escape}?sort=date"
               class="{if $current_sort == 'date'}active{/if}">Date</a>
            <a href="{$base_url}/category/{$category.slug|escape}?sort=views"
               class="{if $current_sort == 'views'}active{/if}">Views</a>
        </div>

        {if $posts}
            <div class="cards-grid">
                {foreach $posts as $post}
                    {include file="partials/post_card.tpl" post=$post}
                {/foreach}
            </div>

            {include file="partials/pagination.tpl"
                current_page=$current_page
                total_pages=$total_pages
                pagination_base_url=$pagination_base_url}
        {else}
            <p>No posts in this category yet.</p>
        {/if}
    </div>
</div>
{/block}
