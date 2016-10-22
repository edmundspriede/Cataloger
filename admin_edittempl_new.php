<?php
if ( !is_object( cmsms() ) ) exit;
if ( !$this->CheckAccess() ) exit;

if (isset($params['submit']) || isset($params['apply']))
{
  if (! empty($params['template_id']))
  {
    // updating a template
    $query = 'UPDATE '
             . cms_db_prefix()
             . 'module_catalog_template set title=?, template=?, type_id=? WHERE id=?';
    
    $qArr = array($params['title'],
                  $params['templ'],
                  $params['type_id'], 
                  $params['template_id']);
             
    $dbresult = $db->Execute($query, $qArr);
                             
    $template_id = $params['template_id'];
  }
  else
  {
    // creating a template
    $query = 'INSERT INTO '
             . cms_db_prefix()
             . 'module_catalog_template (id, title, type_id, template) VALUES (?,?,?,?)';
             
    $template_id = $db->GenID(cms_db_prefix().'module_catalog_template_seq');
    
    $qArr = array($template_id,
                  $params['title'],
                  $params['type_id'],
                  $params['templ']);
    
    $dbresult = $db->Execute($query, $qArr);
  }

  # this DOES clear Cataloger cached files (?????)
  $this->clear_cache();
  
  // force a cache clear? # note: from this point on, things seem to be duplicated (JM)
  $this->DeleteTemplate('catalog_' . $template_id);
  // and recreate
  $this->SetTemplate('catalog_' . $template_id, $params['templ']);

  $params['message'] = $this->Lang('templateupdated');

  if (isset($params['submit']))
  {
    $this->DoAction('defaultadmin', $id, $params);
    return;
  }
}

$typeids = array();
$typeids = catTemplateOperations::GetCatalogerTemplateTypes();
#echo '<------>';
#print_r($typeids);
#die();

/* old method JM
$query = 'SELECT type_id, name FROM ' .
cms_db_prefix(). 'module_catalog_template_type';
$dbresult = $db->Execute($query);
while ($dbresult !== false && $row = $dbresult->FetchRow())
{
  $typeids[$row['name']] = $row['type_id'];
}
*/
if (isset($params['template_id']))
{
  // editing a template
  
  $query = 'SELECT title, template, type_id FROM ' 
           . cms_db_prefix()
           . 'module_catalog_template WHERE id=?';
  
  $dbresult = $db->Execute($query,array($params['template_id']));
  
  $row = $dbresult->FetchRow();
  $templateid = $params['template_id'];
  $title=$row['title'];
  $template=$row['template'];
  $type_id=$row['type_id'];
  $this->smarty->assign('op', $this->Lang('edittemplate'));
}
else
{
  // adding a template
  $templateid = '';
  $title='';
  $template='';
  $this->smarty->assign('op', $this->Lang('addtemplate'));
}
        
$query = "SELECT attribute, alias, type_id FROM "
         . cms_db_prefix()
         . "module_catalog_attr";
         
$dbresult = $db->Execute($query);
        
$attrs = '<h3>'.$this->Lang('title_item_template_vars')
         . '</h3>{$title}, {$notes}, ';
         
$cattrs = '<h3>'
          . $this->Lang('title_cat_template_vars')
          . '</h3>{$title}, {$notes}, {$prev}, {$prevurl}, {$navstr}, {$next}, {$nexturl}, {$items}, ';
          
$pcattrs = '<h3>'
           . $this->Lang('title_catalog_template_vars')
           . '</h3>{$items}, {$attrlist}, {$root_url}, {$image_root}, ';
           
$compattrs = '<h3>'
             . $this->Lang('title_compare_template_vars')
             . '</h3>{$items}, {$attrlist}, {$root_url}, {$image_root}';
             
$feattrs = '<h3>'
           . $this->Lang('title_feature_template_vars')
           . '</h3>{$items}, {$root_url}, {$image_root}';

while ($dbresult !== false && $row = $dbresult->FetchRow())
{
  $safeattr = strtolower(preg_replace('/\W/','',$row['attribute']));
  
  if ($row['type_id'] == CTEMPLATE_ITEM)
  {
    $attrs .= '{$'.$safeattr.'}, ';
    if ($row['alias'] != '')
    {
      $attrs .= '{$'.$row['alias'].'}, ';
    }
  }
  else if ($row['type_id'] == CTEMPLATE_CATEGORY)
  {
    $cattrs .= '{$'.$safeattr.'}, ';
    
    if ($row['alias'] != '')
    {
      $cattrs .= '{$'.$row['alias'].'}, ';
    }
  }
  else if ($row['type_id'] == CTEMPLATE_CATALOG)
  {
    $pcattrs .= '{$'.$safeattr.'}, ';
    
    if ($row['alias'] != '')
    {
      $pcattrs .= '{$'.$row['alias'].'}, ';
    }
  }
}

$image_count = $this->GetPreference('item_image_count', '1');

for ($i=1;$i<=$image_count;$i++)
{
  $attrs .= '{$image_'.$i.'_url}, {$image_thumb_'.$i;
  $attrs .= '_url}, {$src_image_'.$i.'_url}, ';
}

$attrs .= '{$image_url_array}, ';
$attrs .= '{$src_image_url_array}, ';
$attrs .= '{$image_thumb_url_array}';
$file_count = $this->GetPreference('item_file_count', 0);

for ($i=1;$i<=$file_count;$i++)
{
  $attrs .= '{$file_count}, {$file_'.$i.'_name}, {$file_'.$i.'_url}, {$file_'.$i;
  $attrs .= '_ext}, ';
}

$attrs .= '{$file_url_array}, {$file_name_array}, ';
$attrs .= '{$file_ext_array}, {$root_url}, {$image_root}';
$attrs = rtrim($attrs,', ');

$image_count = $this->GetPreference('category_image_count', '1');

for ($i=1;$i<=$image_count;$i++)
{
  $cattrs .= '{$image_'.$i.'_url}, {$image_thumb_'.$i;
  $cattrs .= '_url}, {$src_image_'.$i.'_url}, ';
}

$cattrs .= '{$image_url_array}, ';
$cattrs .= '{$src_image_url_array}, ';
$cattrs .= '{$image_thumb_url_array}, {$root_url}, {$image_root}';
$cattrs = rtrim($cattrs,', ');
$cattrs .= '<h3>$items array contents:</h3>';
$cattrs .= '$items[].title, $items[].link, $items[].image, $items[].cat, $items[].<i>attribute_name</i>, $items[].<i>attribute_alias</i>';
$pcattrs .= '<h3>$items array contents:</h3>';
$pcattrs .= '$items[].title, $items[].link, $items[].image, $items[].cat, $items[].<i>attribute_name</i>, $items[].<i>attribute_alias</i>';
$pcattrs .= '<h3>$attrlist array contents:</h3>';
$pcattrs .= '$attrlist[]->attr, $attrlist[]->safe';
$compattrs .= '<h3>$items array contents:</h3>';
$compattrs .= '$items[].title, $items[].link, $items[].image, $items[].<i>attribute_name</i>, $items[].<i>attribute_alias</i>';
$compattrs .= '<h3>$attrlist array contents:</h3>';
$compattrs .= '$attrlist[]->attr, $attrlist[]->safe';
$feattrs .= '<h3>$items array contents:</h3>';
$feattrs .= '$items[].title, $items[].link, $items[].image, $items[].cat, $items[].<i>attribute_name</i>, $items[].<i>attribute_alias</i>';

###############################################################################################################
###############################################################################################################
/**
* Smarty stuff
*/
###############################################################################################################
###############################################################################################################
// Give the form id to smarty
$smarty->assign('formid', $id);

$this->smarty->assign('startform', $this->CreateFormStart($id, 'submitprefs', $returnid));
$this->smarty->assign('endform', $this->CreateFormEnd());
$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', 'Submit'));

$tab_headers = $this->StartTabHeaders()
               . $this->SetTabHeader('item_page_templates',         $this->Lang('item_page_templates_tab'))
               . $this->SetTabHeader('category_page_templates',     $this->Lang('category_page_templates_tab'))
               . $this->SetTabHeader('item_page_templates',         $this->Lang('title_category_image_tab'))
               . $this->SetTabHeader('item_page_templates',         $this->Lang('title_printable_tab'))
               . $this->SetTabHeader('item_page_templates',         $this->Lang('title_image_tab'))
               . $this->SetTabHeader('item_page_templates',         $this->Lang('title_path_tab'))
               . $this->EndTabHeaders()
               . $this->StartTabContent();

$this->smarty->assign('tab_headers', $tab_headers);
                      
$this->smarty->assign('end_tab', $this->EndTab());

$this->smarty->assign('tab_footers', $this->EndTabContent());

$this->smarty->assign('start_item_image_tab', $this->StartTab('itemimage'));
$this->smarty->assign('start_category_image_tab', $this->StartTab('categoryimage'));
$this->smarty->assign('start_printable_tab', $this->StartTab('printable'));
$this->smarty->assign('start_file_tab', $this->StartTab('file'));
$this->smarty->assign('start_path_tab', $this->StartTab('path'));
$this->smarty->assign('start_image_tab', $this->StartTab('image'));
$this->smarty->assign('title_item_image_count', $this->Lang('title_item_image_count'));
$this->smarty->assign('title_item_file_count', $this->Lang('title_item_file_count'));
$this->smarty->assign('title_item_file_types', $this->Lang('title_item_file_types'));
$this->smarty->assign('title_flush_cats', $this->Lang('title_flush_cats'));

      
$this->smarty->assign('startform', $this->CreateFormStart($id, 'edittempl', $returnid));
$this->smarty->assign('endform', $this->CreateFormEnd());
$this->smarty->assign('hidden',$this->CreateInputHidden($id, 'template_id', $templateid));
$this->smarty->assign('title_title',$this->Lang('title_title'));
$this->smarty->assign('title_template',$this->Lang('title_template'));
$this->smarty->assign('title_template_type',$this->Lang('title_template_type'));
$this->smarty->assign('title_avail_attrs',$this->Lang('title_avail_attrs'));

if (isset($type_id))
{
  if ($type_id == CTEMPLATE_ITEM)
  {
    $this->smarty->assign_by_ref('avail_attrs',$attrs);
  }
  else if ($type_id == CTEMPLATE_CATEGORY)
  {
    $this->smarty->assign('avail_attrs',$cattrs);    
  }
  else if ($type_id == CTEMPLATE_CATALOG)
  {
    $this->smarty->assign('avail_attrs',$pcattrs);    
  }
  else if ($type_id == CTEMPLATE_COMPARISON)
  {
  $this->smarty->assign('avail_attrs',$compattrs);    
  }
  else if ($type_id == CTEMPLATE_FEATURE)
  {
    $this->smarty->assign('avail_attrs',$feattrs);    
  }
}
else
{
  $this->smarty->assign('avail_attrs', $attrs . ', ' . $cattrs . ', ' . $feattrs);
}

//    $this->smarty->assign('title_avail_imattrs',$this->Lang('title_avail_imattrs'));
//    $this->smarty->assign('avail_imattrs',$imattrs);
$this->smarty->assign('input_template_type',$this->CreateInputDropdown($id, 'type_id', $typeids, -1, isset($type_id)?$type_id:''));

$this->smarty->assign('message',$params['message']);
$this->smarty->assign('input_title',$this->CreateInputText($id, 'title', $title, 20, 255));
$this->smarty->assign('input_template',$this->CreateTextArea(false, $id, $template, 'templ'));

$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', lang('submit')));
$this->smarty->assign('apply', $this->CreateInputSubmit($id, 'apply', lang('apply')));
echo $this->ProcessTemplate('edittemplate.tpl');

?>
