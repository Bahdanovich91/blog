{if $total_pages > 1}
<nav class="pagination" aria-label="Pagination">
    {if $current_page > 1}
        <a href="{$pagination_base_url}&page={$current_page - 1}">&laquo; Prev</a>
    {else}
        <span class="pagination__disabled">&laquo; Prev</span>
    {/if}

    {for $page=1 to $total_pages}
        {if $page == $current_page}
            <span class="pagination__current">{$page}</span>
        {else}
            <a href="{$pagination_base_url}&page={$page}">{$page}</a>
        {/if}
    {/for}

    {if $current_page < $total_pages}
        <a href="{$pagination_base_url}&page={$current_page + 1}">Next &raquo;</a>
    {else}
        <span class="pagination__disabled">Next &raquo;</span>
    {/if}
</nav>
{/if}
