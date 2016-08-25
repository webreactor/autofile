{include "pagination.tpl" scope=$data}
<table cellspacing="0" cellpadding="0" border="0" align="left" class="table_files">
    <tr>
        {include "table_filters.tpl"}
    </tr>
{foreach from=$data.files item=$file}
    {if $file.type == 'dir' }
        <tr class="dir" file_id="{{$file.url}}">
            <td>
                <p><a href="{$file.url}/{$_filters}">{include "filename.tpl"}</a></p>
            </td>
            <td></td>
            <td>{$file.type}</td>
            <td></td>
            <td>{$file.stat.hmtime}</td>
            <td></td>
        </tr>
    {/if}
{/foreach}

{foreach from=$data.files item=$file}
    {if $file.type != 'dir' }
        {set $size = $file.stat.size.\FileWebView\Utilities::formatSize()}
        <tr class="file {cycle "", "odd"}" file_id="{{$file.url}}">
            <td>
                <p><a href="{$file.url}">{include "filename.tpl"}</a></p>
            </td>
            <td>
                {if $file.type == 'image' }
                    <small>{$file.meta.width}&#215;{$file.meta.height}</small>
                {/if}
            </td>
            <td>
                {if $file.type == 'markdown'}
                    <a href="{$config.thumbs_url}{$file.relative_url}.html">{$file.type}</a>
                {else}
                    {if $file.type == 'image'}
                        <a href="{$config.thumbs_url}{$file.relative_url}.jpg">{$file.type}</a>
                    {else}
                        {$file.type}
                    {/if}
                {/if}
            </td>
            <td><small>{$size}</small></td>
            <td>{$file.stat.hmtime}</td>
            <td>{{$file.stat.username}}</td>
            <td><a href="#{$file.url}" class="pointer">#</a></td>
        </tr>
    {/if}
{/foreach}
</table>
<div class="total">Всего: {$data.total}</div>
{include "pagination.tpl" scope=$data}
{cache timeout=2 id=$request.document_relative_path}
    {include "readme.tpl" scope=$blocks->readme($request, $data.all_files)}
{/cache}
