<!DOCTYPE html>
<html>
<head>
	<title>{$config.name} | /{{$request.document_relative_path}}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <script type="text/javascript" src="{$config.base_url}.static/js/jquery-3.1.0.min.js"></script>
    <script type="text/javascript" src="{$config.base_url}.static/js/highlight.min.js"></script>
    <script type="text/javascript" src="{$config.base_url}.static/js/main.js"></script>
    <link href="{$config.base_url}.static/style/style.css" type="text/css" rel="stylesheet">
    <link href="{$config.base_url}.static/style/github-markdown.css" type="text/css" rel="stylesheet">
    <link href="{$config.base_url}.static/style/github-highlight.css" type="text/css" rel="stylesheet">
    <link rel="shortcut icon" href="{$config.base_url}.static/img/favicon.ico" type="image/x-icon">
    <style type="text/css">
        audio, video {no_parse}{{/no_parse}
            width: {$config.thumbs.width}px;
        {no_parse}}{/no_parse}
    </style>
{set $document_relative_path_len = $request.document_relative_path.strlen()}
</head>
<body>
<div id="main">
    <div id="header">
        <div id="path">
            {set $path = explode('/', $request.document_relative_path.trim('/')) }
            {set $path_size = $path.count()}
            {set $_link_up = $config.base_url}
            {if $request.document_relative_path == '' }
             / home
            {else}
             / <a href="{$config.base_url}{$_filters}">home</a>
                {foreach from=$path item=$item key=$key}
                    {if $key + 1 == $path_size}
                        / {{$item}}
                    {else}
                        {set $_link_up .= $item.rawurlencode() . '/' }
                         / <a href="{$_link_up}{$_filters}">{{$item}}</a>
                    {/if}
                {/foreach}
            {/if}
        </div>
        <div id="filters">
            <form action="{$request.document_url}{$_filters}" method="get">
            {if $request.sort_direction == 'asc' }
                {set $oposite_sort = 'desc'}
            {else}
                {set $oposite_sort = 'asc'}
            {/if}
            <div class="filters_block">
                Сортировать по:
                    {if $request.sort == 'name' }
                        <a href="{$request.document_url}{$controller->filters($request,array('sort_direction'=>$oposite_sort))}">имeни</a>
                        <span class="arrow">{if $request.sort_direction != 'asc' }&uarr;{else}&darr;{/if}</span>
                    {else}
                        <a href="{$request.document_url}{$controller->filters($request,array('sort'=>'name'))}">имeни</a>
                        <span class="arrow"></span>
                    {/if}
                    {if $request.sort == 'size' }
                        <a href="{$request.document_url}{$controller->filters($request,array('sort_direction'=>$oposite_sort))}">размеру</a>
                        <span class="arrow">{if $request.sort_direction != 'asc' }&uarr;{else}&darr;{/if}</span>
                    {else}
                        <a href="{$request.document_url}{$controller->filters($request,array('sort'=>'size'))}">размеру</a>
                        <span class="arrow"></span>
                    {/if}
                    {if $request.sort == 'mtime' }
                        <a href="{$request.document_url}{$controller->filters($request,array('sort_direction'=>$oposite_sort))}">дате&nbsp;модификации</a>
                            <span class="arrow">{if $request.sort_direction != 'asc' }&uarr;{else}&darr;{/if}</span>
                    {else}
                        <a href="{$request.document_url}{$controller->filters($request,array('sort'=>'mtime'))}">дате&nbsp;модификации</a>
                        <span class="arrow"></span>
                    {/if}
            </div>

            <div class="filters_block">
                Отобразить:
                {if $request.view_mode == 'float' }эскизами{else}<a href="{$request.document_url}{$controller->filters($request,array('view_mode'=>'float'))}">эскизами</a>{/if},
                {if $request.view_mode == 'table' }таблицей{else}<a href="{$controller->filters($request,array('view_mode'=>'table'))}">таблицей</a>{/if},
                {if $request.view_mode == 'mixed' }наглядно{else}<a href="{$controller->filters($request,array('view_mode'=>'mixed'))}">наглядно</a>{/if}.
            </div>

            <div class="filters_block">
                <input class="search" type="search" name='q' value="{$request.search}" placeholder="Поиск" onsearch="submit()">
            </div>
            </form>
        </div>
    </div>
    {include "left_menu.tpl" scope=$blocks->leftMenu($request)}
    <div id="content" class="page_column">
            {include $request.template}
            <div id="pusher">&nbsp;</div>
    </div>
    <br style="clear:both;" />
</div>

</body>
</html>
