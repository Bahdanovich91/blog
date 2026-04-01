<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{block name="title"}{$page_title|default:'Blogy'}{/block}</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>

{include file="partials/header.tpl"}

<main>
    {block name="content"}{/block}
</main>

{include file="partials/footer.tpl"}

</body>
</html>
