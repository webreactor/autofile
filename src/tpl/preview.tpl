{if $file.type == 'image'}
    <a href="{$file.url}">
        <img src="{$config.thumbs_url}{$file.relative_url}.jpg" 
title="{$file.meta.width}&#215;{$file.meta.height}
{$file.stat.ctime.\FileWebView\Utilities::tsToDateTime()}
{{$file.stat.username}}">
    </a>
{/if}
{if $file.type == 'video'}
    <video controls preload="none">
      <source src="{$file.url}">
    </video>
{/if}
{if $file.type == 'audio'}
    <audio controls preload="none">
      <source src="{$file.url}">
    </audio>
{/if}
{if $file.type == 'markdown'}
    <a href="{$config.thumbs_url}{$file.relative_url}.html">view</a>
{/if}
