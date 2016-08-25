<table cellspacing="0" cellpadding="0" border="0" align="left" class="table_files">
<tr>
    <td></td>
    <td>
        {if $request.sort == 'name' }
            <a class="normal" href="{$request.document_url}{$controller->filters($request,array('sort_direction'=>$oposite_sort))}">Имя</a>
            <span class="arrow">{if $request.sort_direction != 'asc' }&uarr;{else}&darr;{/if}</span>
        {else}
            <a class="normal" href="{$request.document_url}{$controller->filters($request,array('sort'=>'name'))}">Имя</a>
            <span class="arrow"></span>
        {/if}
    </td><td></td>
    <td>
        {if $request.sort == 'type' }
            <a class="normal" href="{$controller->filters($request,array('sort_direction'=>$oposite_sort))}">Тип</a>
            <span class="arrow">{if $request.sort_direction != 'asc' }&uarr;{else}&darr;{/if}</span>
        {else}
            <a class="normal" href="{$controller->filters($request,array('sort'=>'type'))}">Тип</a>
            <span class="arrow"></span>
        {/if}
    </td>
    <td>
        {if $request.sort == 'size' }
            <a class="normal" href="{$controller->filters($request,array('sort_direction'=>$oposite_sort))}">Размер</a>
            <span class="arrow">{if $request.sort_direction != 'asc' }&uarr;{else}&darr;{/if}</span>
        {else}
            <a class="normal" href="{$controller->filters($request,array('sort'=>'size'))}">Размер</a>
            <span class="arrow"></span>
        {/if}
    </td>
    <td>
        {if $request.sort == 'mtime' }
            <a class="normal" href="{$controller->filters($request,array('sort_direction'=>$oposite_sort))}">Дата&nbsp;модификации</a>
                <span class="arrow">{if $request.sort_direction != 'asc' }&uarr;{else}&darr;{/if}</span>
        {else}
            <a class="normal" href="{$controller->filters($request,array('sort'=>'mtime'))}">Дата&nbsp;модификации</a>
            <span class="arrow"></span>
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
        <tr class="dir" file_id="{$file.relative_name.rawurlencode()}">
            <td></td>
            <td>
                <p><a href="{$config.base_url}{$file.relative_name.\FileWebView\Utilities::urlEncodePath()}/{$_filters}">{{$file.name}}</a></p>
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
            {if $file.type == 'image'}
                <a href="{$request.document_url}{$file.name.rawurlencode()}">
                    <img src="{$config.thumbs_url}{$config.base_url}{$file.relative_name.\FileWebView\Utilities::urlEncodePath()}.jpg" 
                    title="{$file.meta.width}&#215;{$file.meta.height}{@ "\n"}{$file.stat.ctime.\FileWebView\Utilities::tsToDateTime()}">
                </a>
            {/if}
            {if $file.type == 'video'}
                <video controls preload="none">
                  <source src="{$config.base_url}{$file.relative_name.\FileWebView\Utilities::urlEncodePath()}">
                </video>
            {/if}
            {if $file.type == 'audio'}
                <audio controls preload="none">
                  <source src="{$config.base_url}{$file.relative_name.\FileWebView\Utilities::urlEncodePath()}">
                </audio>
            {/if}
            {if $file.type == 'markdown'}
                <a href="{$config.thumbs_url}{$config.base_url}{$file.relative_name.\FileWebView\Utilities::urlEncodePath()}.html">view</a>
            {/if}
        </td>
        <td>
            <a href="{$request.document_url}{$file.name.rawurlencode()}">{{$file.name}}</a>
        </td>
        <td>
            {if $file.type == 'image'}
                {$file.meta.width}&#215;{$file.meta.height}
            {/if}
        </td>
        <td>{$file.type}</td>
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
