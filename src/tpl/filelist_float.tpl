{include "pagination.tpl" scope=$data}
<div class="float_files">
    <div class="float_group">
    {foreach from=$data.files item=$file}
        {if $file.type == 'dir' }
            <div class="dir"><a href="{$request.document_url}{$file.name.rawurlencode()}/{$_filters}"
                title="{$file.stat.hmtime}"
                >{{$file.relative_name.substr($document_relative_path_len)}}</a></div>
        {/if}
    {/foreach}
    </div>
    <div class="float_group">
        {foreach from=$data.files item=$file}
            {set $size = $file.stat.size.\FileWebView\Utilities::formatSize()}
            {if $file.type == 'image' }
                <div class="float_container" file_id="{{$file.url}}">
                    {include "preview.tpl"}
                    <div class="subtitile">
                        <a href="{$file.url}">{{$file.relative_name.substr($document_relative_path_len)}}</a> <small>{$size}</small>
                        <a href="#{$file.url}" class="pointer">#</a>
                    </div>
                </div>
            {/if}
        {/foreach}
    </div>
    <div class="float_group">
        {set $cc = 0 }
        {foreach from=$data.files item=$file}
            {if $file.type == 'video' }
            {set $size = $file.stat.size.\FileWebView\Utilities::formatSize()}
                <div class="float_container" file_id="{{$file.url}}">
                    {include "preview.tpl"}
                    <div class="subtitile">
                        <a href="{$file.url}"
title="{$file.stat.hmtime}
{{$file.stat.username}}"
                        >{{$file.relative_name.substr($document_relative_path_len)}}</a> <small>{$size}</small>
                        <a href="#{$file.url}" class="pointer">#</a>
                    </div>
                </div>
            {/if}
        {/foreach}
    </div>
    <div class="float_group">
        {foreach from=$data.files item=$file}
            {if $file.type != 'image' && $file.type != 'video' && $file.type != 'dir'}
                {set $size = $file.stat.size.\FileWebView\Utilities::formatSize()}
                <div class="other_file" file_id="{{$file.url}}">
                    {include "preview.tpl"}
                    <div class="subtitile">
                        <a href="{$file.url}"
title="{$file.stat.hmtime}
{{$file.stat.username}}"
                        >{{$file.relative_name.substr($document_relative_path_len)}}</a> <small>{$size}</small>
                        <a href="#{$file.url}" class="pointer">#</a>
                    </div>
                </div>
            {/if}
        {/foreach}
    </div>
</div>
<div class="total">Всего: {$data.total}</div>
{include "pagination.tpl" scope=$data}
{cache timeout=2 id=$request.document_relative_path}
    {include "readme.tpl" scope=$blocks->readme($request, $data.all_files)}
{/cache}
