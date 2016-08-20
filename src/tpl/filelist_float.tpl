<div class="float_files">
    <div class="float_group">
    {foreach from=$data item=$file}
        {if $file.type == 'dir' }
            <div class="dir"><a href="{$request.document_url}{$file.name.rawurlencode()}/{$_filters}"
                title="{$file.stat.ctime.\FileWebView\Utilities::tsToDateTime()}"
                >{{$file.name}}</a></div>
        {/if}
    {/foreach}
    </div>
    <div class="float_group">
        {foreach from=$data item=$file}
            {set $size = $file.stat.size.\FileWebView\Utilities::formatSize()}
            {if $file.type == 'image' }
                <div class="float_container" file_id="{$file.name.rawurlencode()}">
                    <a href="{$request.document_url}{$file.name.rawurlencode()}">
                        <img src="{$config.thumbs_url}{$request.document_relative_url}{$file.name.rawurlencode()}.jpg"
                        title="{$file.meta.width}&#215;{$file.meta.height}{@ "\n"}{$file.stat.ctime.\FileWebView\Utilities::tsToDateTime()}">
                    </a>
                    <div class="subtitile">
                        <a href="{$request.document_url}{$file.name.rawurlencode()}">{{$file.name}}</a> <small>{$size}</small>
                        <a href="#{$file.name.rawurlencode()}" class="pointer">#</a>
                    </div>
                </div>
            {/if}
        {/foreach}
    </div>
    <div class="float_group">
        {set $cc = 0 }
        {foreach from=$data item=$file}
            {if $file.type == 'video' }
            {set $size = $file.stat.size.\FileWebView\Utilities::formatSize()}
                <div class="float_container" file_id="{$file.name.rawurlencode()}">
                    <video controls preload="none">
                      <source src="{$request.document_url}{$file.name.rawurlencode()}">
                    </video>
                    <div class="subtitile">
                        <a href="{$request.document_url}{$file.name.rawurlencode()}"
                        title="{$file.stat.ctime.\FileWebView\Utilities::tsToDateTime()}"
                        >{{$file.name}}</a> <small>{$size}</small>
                        <a href="#{$file.name.rawurlencode()}" class="pointer">#</a>
                    </div>
                </div>
            {/if}
        {/foreach}
    </div>
    <div class="float_group">
        {foreach from=$data item=$file}
            {if $file.type != 'image' && $file.type != 'video' && $file.type != 'dir'}
                {set $size = $file.stat.size.\FileWebView\Utilities::formatSize()}
                <div class="other_file" file_id="{$file.name.rawurlencode()}">
                    {if $file.type == 'audio'}
                        <audio controls preload="none">
                          <source src="{$request.document_url}{$file.name.rawurlencode()}">
                        </audio>
                    {/if}
                    <div class="subtitile">
                        <a href="{$request.document_url}{$file.name.rawurlencode()}"
                        title="{$file.stat.ctime.\FileWebView\Utilities::tsToDateTime()}"
                        >{{$file.name}}</a> <small>{$size}</small>
                        {if $file.type == 'markdown'}
                            <a href="{$config.thumbs_url}{$request.document_relative_url}{$file.name.rawurlencode()}.html">view</a>
                        {/if}
                        <a href="#{$file.name.rawurlencode()}" class="pointer">#</a>
                    </div>
                </div>
            {/if}
        {/foreach}
    </div>
</div>
{cache timeout=2 id=$request.document_relative_path}
    {include "readme.tpl" scope=$blocks->readme($request, $data)}
{/cache}
