{extends file="layout.tpl"}

{block name="title"}Blogy &mdash; Home{/block}

{block name="content"}
    {foreach $sections as $section}
        <section class="category-section">
            <div class="container">
                <div class="category-section__header">
                    <h2 class="category-section__name">{$section.category.name|escape|upper}</h2>
                    <a href="/category/{$section.category.slug|escape}" class="category-section__view-all">View All</a>
                </div>

                <div class="cards-grid">
                    {foreach $section.posts as $post}
                        {include file="partials/post_card.tpl" post=$post}
                    {/foreach}
                </div>
            </div>
        </section>
        {foreachelse}
        <section class="category-section">
            <div class="container">
                <p>No posts available yet. Check back soon!</p>
            </div>
        </section>
    {/foreach}
{/block}
