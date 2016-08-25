{if $total_pages > 1}
    <div class="pagination">Страница:
    {for from=1 to=$total_pages+1 key=$key}
        {if $global.request.page != $key}
            <a href="{$global.request.document_url}{$global.controller->filters($global.request,array('page'=>$key))}">{$key}</a>
        {else}
            <span class="current">{$key}</span>
        {/if}
    {/for}
    </div>
{/if}
