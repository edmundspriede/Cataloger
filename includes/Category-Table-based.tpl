{assign var="cols" value="3"}
{section name=numimg loop=$image_url_array}<img src="{$image_url_array[numimg]}" title="{title}" alt="{title}" />{/section}
<table style="border: solid 1px black;">
<tr>
    {section name=numloop loop=$items}
        <td style="width: 200px;"><a href="{$items[numloop].link}"><img src="{$items[numloop].image}" title="{$items[numloop].title}" alt="{$items[numloop].title}"/></a><br /><a href="{$items[numloop].link}">{$items[numloop].title}</a></td>
        {if not ($smarty.section.numloop.rownum mod $cols)}
                {if not $smarty.section.numloop.last}
                        </tr><tr>
                {/if}
        {/if}
        {if $smarty.section.numloop.last}
                {math equation = "n - a % n" n=$cols a=$items|@count assign="cells"}
                {if $cells ne $cols}
                {section name=pad loop=$cells}
                        <td style="width: 200px;">&nbsp;</td>
                {/section}
                {/if}
                </tr>
        {/if}
    {/section}
    {if $hasnav == 1}
<tr><td{if $cols gt 1} colspan="{$cols}"{/if} style="border-top: solid 1px black;">{$prev}{$navstr}{$next}</td></tr>
	{/if}
</table>
