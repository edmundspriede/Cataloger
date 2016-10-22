{* Top links nav *}
{$innernav}

{* admin user messages *}
<h3>{$section}</h3>
{if $message != ''}
  <h4>{$message}</h4>
{/if}

{* TABS *}
{$tab_headers}
  {$start_item_page_templates_tab}
    <table cellspacing="0" cellpadding="0" class="pagetable">
      <thead>
        <tr>
          <th>{$title_template}</th>
          <th class="pageicon">&nbsp;</th>
          <th class="pageicon">&nbsp;</th>
        </tr>
      </thead>
      <tbody>
        {foreach from=$PageItems item=entry}
          {cycle values="row1,row2" assign='rowclass'}

          <tr class="{$rowclass}" onmouseover="this.className='{$rowclass}hover';" onmouseout="this.className='{$rowclass}';">
            <td>{$entry->title}</td>
            <td>{$entry->editlink}</td>
            <td>{$entry->deletelink}</td>
          </tr>
        {/foreach}
      </tbody>    
    </table>
    <div class="pageoptions">
      <p class="pageoptions">{$addlink}</p>
    </div>
  {$end_tab}
  
  {$start_category_page_templates_tab}
    <table cellspacing="0" cellpadding="0" class="pagetable">
      <thead>
        <tr>
          <th>{$title_template}</th>
          <th class="pageicon">&nbsp;</th>
          <th class="pageicon">&nbsp;</th>
        </tr>
      </thead>
      <tbody>
        {foreach from=$PageCategories item=entry}
          {cycle values="row1,row2" assign='rowclass'}

          <tr class="{$rowclass}" onmouseover="this.className='{$rowclass}hover';" onmouseout="this.className='{$rowclass}';">
            <td>{$entry->title}</td>
            <td>{$entry->editlink}</td>
            <td>{$entry->deletelink}</td>
          </tr>
        {/foreach}
      </tbody>    
    </table>
    <div class="pageoptions">
      <p class="pageoptions">{$addlink}</p>
    </div>
  {$end_tab}
  
  {$start_printable_catalog_templates_tab}
    <table cellspacing="0" cellpadding="0" class="pagetable">
      <thead>
        <tr>
          <th>{$title_template}</th>
          <th class="pageicon">&nbsp;</th>
          <th class="pageicon">&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$PrintableCatalogs item=entry}
          {cycle values="row1,row2" assign='rowclass'}

          <tr class="{$rowclass}" onmouseover="this.className='{$rowclass}hover';" onmouseout="this.className='{$rowclass}';">
            <td>{$entry->title}</td>
            <td>{$entry->editlink}</td>
            <td>{$entry->deletelink}</td>
          </tr>
        {/foreach}
      </tbody>    
    </table>
    <div class="pageoptions">
      <p class="pageoptions">{$addlink}</p>
    </div>
  {$end_tab}
  
  {$start_item_comparison_templates_tab}
    <table cellspacing="0" cellpadding="0" class="pagetable">
      <thead>
        <tr>
          <th>{$title_template}</th>
          <th class="pageicon">&nbsp;</th>
          <th class="pageicon">&nbsp;</th>
        </tr>
      </thead>
      <tbody>
        {foreach from=$ItemComparisons item=entry}
          {cycle values="row1,row2" assign='rowclass'}

          <tr class="{$rowclass}" onmouseover="this.className='{$rowclass}hover';" onmouseout="this.className='{$rowclass}';">
            <td>{$entry->title}</td>
            <td>{$entry->editlink}</td>
            <td>{$entry->deletelink}</td>
          </tr>
        {/foreach}
      </tbody>    
    </table>
    <div class="pageoptions">
      <p class="pageoptions">{$addlink}</p>
    </div>
  {$end_tab}
  
  {$start_feature_list_templates_tab}
    <table cellspacing="0" cellpadding="0" class="pagetable">
      <thead>
        <tr>
          <th>{$title_template}</th>
          <th class="pageicon">&nbsp;</th>
          <th class="pageicon">&nbsp;</th>
        </tr>
      </thead>
      <tbody>
        {foreach from=$FeatureLists item=entry}
          {cycle values="row1,row2" assign='rowclass'}

          <tr class="{$rowclass}" onmouseover="this.className='{$rowclass}hover';" onmouseout="this.className='{$rowclass}';">
            <td>{$entry->title}</td>
            <td>{$entry->editlink}</td>
            <td>{$entry->deletelink}</td>
          </tr>
        {/foreach}
      </tbody>    
    </table>
    <div class="pageoptions">
      <p class="pageoptions">{$addlink}</p>
    </div>
  {$end_tab}
{$tab_footers}
{* TODO: make sure we have one {$addlink-type-} per template type *}