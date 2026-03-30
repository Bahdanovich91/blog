<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{$page_title|escape}</title>
</head>
<body>
<h1>{$page_title|escape}</h1>

{if $sections|@count > 0}
    {foreach $sections as $section}
        <h2>{$section.category.name|escape}</h2>
        <ul>
            {foreach $section.posts as $post}
                <li>
                    <strong>{$post.title|escape}</strong><br>
                    {$post.content|escape}
                </li>
            {/foreach}
        </ul>
    {/foreach}
{else}
    <p>No categories or posts found.</p>
{/if}
</body>
</html>