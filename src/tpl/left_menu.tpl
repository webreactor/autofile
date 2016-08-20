<div id="side_menu" class="page_column">
    {if $global.request.document_relative_path != '' }
        <div class="back"><a href="{$global._link_up}{$global._filters}">&larr;&nbsp;Назад</a></div>
    {/if}
    {foreach from=$data item=$file}

        {if $file.type == 'dir'}
            {if $global.request.document_relative_path == $file.relative_name .'/'}
                <span>{{$file.name}}</span>
            {else}
                <a href="{$global._link_up}{$file.name.rawurlencode()}/{$global._filters}">{{$file.name}}</a>
            {/if}
        {/if}
    {/foreach}
</div>
