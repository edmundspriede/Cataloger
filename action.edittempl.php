<?php
if( !defined('CMS_VERSION') ) exit;
if (! $this->CheckAccess()) exit;

$this->initAdminNav($id, $params, $returnid);

$params['message']='';
if (isset($params['submit']) || isset($params['apply']))
{
	if (! empty($params['template_id']))
	{
		// updating a template
		$query = 'UPDATE '
              . cms_db_prefix()
              . 'module_catalog_template set title=?, template=?, type_id=? WHERE id=?';
              
		$dbresult = $db->Execute(
                              $query,
                              array(
                                      $params['title'],
                                      $params['templ'],
			                                $params['type_id'], 
                                      $params['template_id']
                                    )
                            );
                            
		$template_id = $params['template_id'];
	}
	else
	{
		// creating a template
		$query = 'INSERT INTO '
              . cms_db_prefix()
              . 'module_catalog_template (id, title, type_id, template) VALUES (?,?,?,?)';
              
		$template_id = $db->GenID(cms_db_prefix() . 'module_catalog_template_seq');
    
		$dbresult = $db->Execute(
                              $query,
                              array(
                                      $template_id,
                                      $params['title'],
                                      $params['type_id'],
                                      $params['templ']
                                    )
                            );
	}

	// force a cache clear?
	$this->DeleteTemplate('catalog_'.$template_id);
	// and recreate
	$this->SetTemplate('catalog_'.$template_id,$params['templ']);

	$params['message'] = $this->Lang('templateupdated');
	
	if (isset($params['submit']))
	{
		$this->DoAction('defaultadmin', $id, $params);
		return;
	}
}


$typeids = array();
$query = 'SELECT type_id, name FROM ' 
         . cms_db_prefix()
         . 'module_catalog_template_type';
         
$dbresult = $db->Execute($query);

while ($dbresult !== false && $row = $dbresult->FetchRow())
{
  $typeids[$row['name']] = $row['type_id'];
}

if (isset($params['template_id']))
{
	// editing a template
	$query = 'SELECT title, template, type_id FROM '
            . cms_db_prefix()
            . 'module_catalog_template WHERE id=?';
            
	$dbresult = $db->Execute( $query, array( $params['template_id']) );
  
	$row = $dbresult->FetchRow();
	$templateid = $params['template_id'];
	$title=$row['title'];
	$template=$row['template'];
	$type_id=$row['type_id'];
	$smarty->assign('op', $this->Lang('edittemplate'));
}
else
{
	// adding a template
	$templateid = '';
	$title = '';
	$template = '';
	$smarty->assign( 'op', $this->Lang('addtemplate') );
}

$query = "SELECT attribute, alias, type_id FROM " . cms_db_prefix() . "module_catalog_attr";
$dbresult = $db->Execute($query);
        
$attrs_title = '<h3>' . $this->Lang('title_item_template_vars') . '</h3>';
$attrs_array = array(
                      '{$title}',
                      '{$main_content}'
                    );
                    
$cattrs_title = '<h3>' . $this->Lang('title_cat_template_vars') . '</h3>';
$cattrs_array = array(
                        '{$title}',
                        '{$main_content}',
                        '{$prev}',
                        '{$prevurl}',
                        '{$navstr}',
                        '{$next}',
                        '{$nexturl}',
                        '{$items}'
                      );
                    

$pcattrs_title = '<h3>' . $this->Lang('title_catalog_template_vars') . '</h3>';
$pcattrs_array = array(
                        '{$items}',
                        '{$main_content}',
                        '{$attrlist}',
                        '{$root_url}',
                        '{$image_root}'
                      );        

$compattrs_title = '<h3>' . $this->Lang('title_compare_template_vars') . '</h3>';
$compattrs_array = array(
                          '{$items}',
                          '{$main_content}',
                          '{$attrlist}',
                          '{$root_url}',
                          '{$image_root}'
                        ); 

$feattrs_title = '<h3>' . $this->Lang('title_feature_template_vars') . '</h3>';
$feattrs_array = array(
                        '{$items}',
                        '{$main_content}',
                        '{$attrlist}',
                        '{$root_url}',
                        '{$image_root}'
                      );

while ($dbresult !== false && $row = $dbresult->FetchRow())
{
  $safeattr = strtolower(preg_replace('/\W/','',$row['attribute']));
  
  if ($row['type_id'] == CTEMPLATE_ITEM)
  {
    $attrs_array[] = '{$' . $safeattr . '}';

		if ($row['alias'] != '')
		{
			$attrs_array[] = '{$' . $row['alias'] . '}';
		}
  }
  else if ($row['type_id'] == CTEMPLATE_CATEGORY)
  {
		$cattrs_array[] = '{$' . $safeattr . '}';
		
    if ($row['alias'] != '')
		{
			$cattrs_array[] = '{$' . $row['alias'] . '}';
		}
	}
  else if ($row['type_id'] == CTEMPLATE_CATALOG)
  {
		$pcattrs_array[] = '{$' . $safeattr . '}';
		if ($row['alias'] != '')
		{
			$pcattrs_array[] = '{$' . $row['alias'] . '}';
		}
	}
}
          
$image_count = $this->GetPreference('item_image_count', '1');

for ($i=1;$i<=$image_count;$i++)
{
  $attrs_array[] = '{$image_' . $i . '_url}';
  $attrs_array[] = '{$image_thumb_' . $i . '_url}';
}

$attrs_array[] = '{$image_url_array}';
$attrs_array[] = '{$src_image_url_array}';
$attrs_array[] = '{$image_thumb_url_array}';

$file_count = $this->GetPreference('item_file_count', 0);

for ($i=1; $i <= $file_count; $i++)
{
  $attrs_array[] = '{$file_count}, {$file_'.$i.'_name}, {$file_'.$i.'_url}, {$file_'.$i;
  $attrs_array[] = '_ext}, ';
}
    
$attrs_array[] = '{$file_url_array}';
$attrs_array[] = '{$file_name_array}';
$attrs_array[] = '{$file_ext_array}';
$attrs_array[] = '{$root_url}';
$attrs_array[] = '{$image_root}';

$attrs = $attrs_title . '<p><strong>' . implode(', ', $attrs_array). '</strong></p>';

$image_count = $this->GetPreference('category_image_count', '1');

for ($i=1;$i<=$image_count;$i++)
{
  $cattrs_array[] = '{$image_' . $i . '_url}';
  $cattrs_array[] = '{$image_thumb_' . $i . '_url}';
  $cattrs_array[] = '{$src_image_' . $i . '_url}';
}

$cattrs_array[] = '{$image_url_array}, ';
$cattrs_array[] = '{$src_image_url_array}, ';
$cattrs_array[] = '{$image_thumb_url_array}';
$cattrs_array[] = '{$root_url}';
$cattrs_array[] = '{$image_root}';

$cattrs = $cattrs_title . '<p><strong>' . implode(', ', $cattrs_array). '</strong></p>';

$cattrs_it_title = '<h3>$items array contents:</h3>';
$cattrs_it_array = array(
                          '$items[].title',
                          '$items[].link',
                          '$items[].image',
                          '$items[].cat',
                          '$items[].<i>attribute_name</i>',
                          '$items[].<i>attribute_alias</i>'
                        );
                        
$cattrs .= $cattrs_it_title . '<p><strong>' . implode(', ', $cattrs_it_array) . '</strong></p>';

$pcattrs = $pcattrs_title . '<p><strong>' . implode(', ', $pcattrs_array). '</strong></p>';

$pcattrs .= '<h3>$items array contents:</h3>';
$pcattrs .= '$items[].title, $items[].link, $items[].image, $items[].cat, $items[].<i>attribute_name</i>, $items[].<i>attribute_alias</i>';
$pcattrs .= '<h3>$attrlist array contents:</h3>';
$pcattrs .= '$attrlist[]->attr, $attrlist[]->safe';

$compattrs = $compattrs_title . '<p><strong>' . implode(', ', $compattrs_array). '</strong></p>';
$compattrs .= '<h3>$items array contents:</h3>';
$compattrs .= '$items[].title, $items[].link, $items[].image, $items[].<i>attribute_name</i>, $items[].<i>attribute_alias</i>';
$compattrs .= '<h3>$attrlist array contents:</h3>';
$compattrs .= '$attrlist[]->attr, $attrlist[]->safe';

$feattrs = $feattrs_title . '<p><strong>' . implode(', ', $feattrs_array). '</strong></p>';
$feattrs .= '<h3>$items array contents:</h3>';
$feattrs .= '$items[].title, $items[].link, $items[].image, $items[].cat, $items[].<i>attribute_name</i>, $items[].<i>attribute_alias</i>';

    
$smarty->assign('startform', $this->CreateFormStart($id, 'edittempl', $returnid));
$smarty->assign('endform', $this->CreateFormEnd());
$smarty->assign('hidden',$this->CreateInputHidden($id, 'template_id', $templateid));
$smarty->assign('title_title',$this->Lang('title_title'));
$smarty->assign('title_template',$this->Lang('title_template'));
$smarty->assign('title_template_type',$this->Lang('title_template_type'));
$smarty->assign('title_avail_attrs',$this->Lang('title_avail_attrs'));

if (isset($type_id))
{
  if ($type_id == CTEMPLATE_ITEM)
	{
	  $smarty->assign('avail_attrs',$attrs);
	}
  else if ($type_id == CTEMPLATE_CATEGORY)
	{
	  $smarty->assign('avail_attrs',$cattrs);		
	}
  else if ($type_id == CTEMPLATE_CATALOG)
	{
	  $smarty->assign('avail_attrs',$pcattrs);		
	}
  else if ($type_id == CTEMPLATE_COMPARISON)
	{
	  $smarty->assign('avail_attrs',$compattrs);		
	}
  else if ($type_id == CTEMPLATE_FEATURE)
	{
	  $smarty->assign('avail_attrs',$feattrs);		
	}
}
else
{
  $smarty->assign('avail_attrs', $attrs . ', ' . $cattrs.', ' . $feattrs);
}

//$smarty->assign('title_avail_imattrs',$this->Lang('title_avail_imattrs'));		
//$smarty->assign('avail_imattrs',$imattrs);
$smarty->assign('input_template_type',$this->CreateInputDropdown($id, 'type_id', $typeids, -1, isset($type_id)?$type_id:''));

$smarty->assign('message',$params['message']);
$smarty->assign('input_title',$this->CreateInputText($id, 'title', $title, 20, 255));
$smarty->assign('input_template',$this->CreateTextArea(false, $id, $template, 'templ'));

$smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', lang('submit')));
$smarty->assign('apply', $this->CreateInputSubmit($id, 'apply', lang('apply')));
echo $this->ProcessTemplate('edittemplate.tpl');
?>