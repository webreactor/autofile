<table cellspacing="0" cellpadding="0" border="0" align="left" class="table_files">
<tr>
    <td>
        {if $request.sort == 'name' }
            <a class="normal" href="{$controller->filters($request,array('sort_direction'=>$oposite_sort))}">
                Имя</a>&nbsp;{if $request.sort_direction != 'asc' }&uarr;{else}&darr;{/if}
        {else}
            <a class="normal" href="{$controller->filters($request,array('sort'=>'name'))}">Имя</a>&nbsp;
        {/if}
    </td>
    <td></td>
    <td>
    {if $request.sort == 'type' }
        <a class="normal" href="{$controller->filters($request,array('sort_direction'=>$oposite_sort))}">
            Тип</a>&nbsp;{if $request.sort_direction != 'asc' }&uarr;{else}&darr;{/if}
    {else}
        <a class="normal" href="{$controller->filters($request,array('sort'=>'type'))}">Тип</a>&nbsp;
    {/if}
    </td>
    <td>
        {if $request.sort == 'size' }
            <a class="normal" href="{$controller->filters($request,array('sort_direction'=>$oposite_sort))}">
                Размер</a>&nbsp;{if $request.sort_direction != 'asc' }&uarr;{else}&darr;{/if}
        {else}
            <a class="normal" href="{$controller->filters($request,array('sort'=>'size'))}">Размер</a>&nbsp;
        {/if}
    </td>
    <td>
        {if $request.sort == 'mtime' }
            <a class="normal" href="{$controller->filters($request,array('sort_direction'=>$oposite_sort))}">
                Дата&nbsp;модификации</a>&nbsp;{if $request.sort_direction != 'asc' }&uarr;{else}&darr;{/if}
        {else}
            <a class="normal" href="{$controller->filters($request,array('sort'=>'mtime'))}">Дата&nbsp;модификации</a>&nbsp;
        {/if}
    </td>
    <td>
        {if $request.sort == 'username' }
            <a class="normal" href="{$controller->filters($request,array('sort_direction'=>$oposite_sort))}">
                Пользователь</a>&nbsp;{if $request.sort_direction != 'asc' }&uarr;{else}&darr;{/if}
        {else}
            <a class="normal" href="{$controller->filters($request,array('sort'=>'username'))}">Пользователь</a>&nbsp;
        {/if}
    </td>
</tr>
{foreach from=$data item=$file}
    {if $file.type == 'dir' }
        <tr class="dir" file_id="{$file.name.rawurlencode()}">
            <td>
                <p><a href="{$request.document_url}{$file.name.rawurlencode()}/{$_filters}">{{$file.name}}</a></p>
            </td>
            <td></td>
            <td>{$file.type}</td>
            <td></td>
            <td>{$file.stat.mtime.\FileWebView\Utilities::tsToDateTime()}</td>
            <td></td>
        </tr>
    {/if}
{/foreach}

{foreach from=$data item=$file}
    {if $file.type != 'dir' }
        {set $size = $file.stat.size.\FileWebView\Utilities::formatSize()}
        <tr class="file {cycle "", "odd"}" file_id="{$file.name.rawurlencode()}">
            <td>
                <p><a href="{$request.document_url}{$file.name.rawurlencode()}">{{$file.name}}</a></p>
            </td>
            <td>
                {if $file.type == 'image' }
                    <small>{$file.meta.width}&#215;{$file.meta.height}</small>
                {/if}
            </td>
            <td>
                {if $file.type == 'markdown'}
                    <a href="{$config.thumbs_url}{$request.document_relative_url}{$file.name.rawurlencode()}.html">{$file.type}</a>
                {else}
                    {if $file.type == 'image'}
                        <a href="{$config.thumbs_url}{$request.document_relative_url}{$file.name.rawurlencode()}.jpg">{$file.type}</a>
                    {else}
                        {$file.type}
                    {/if}
                {/if}
            </td>
            <td><small>{$size}</small></td>
            <td>{$file.stat.mtime.\FileWebView\Utilities::tsToDateTime()}</td>
            <td>{{$file.stat.username}}</td>
            <td><a href="#{$file.name.rawurlencode()}" class="pointer">#</a></td>
        </tr>
    {/if}
{/foreach}
</table>
{cache timeout=2 id=$request.document_relative_path}
    {include "readme.tpl" scope=$blocks->readme($request, $data)}
{/cache}
