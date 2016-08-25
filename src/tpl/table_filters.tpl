
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
            <a class="normal" href="{$controller->filters($request,array('sort_direction'=>$oposite_sort))}">Пользователь</a>
            <span class="arrow">{if $request.sort_direction != 'asc' }&uarr;{else}&darr;{/if}</span>
        {else}
            <a class="normal" href="{$controller->filters($request,array('sort'=>'username'))}">Пользователь</a>
            <span class="arrow"></span>
        {/if}
    </td>
