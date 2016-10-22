<?php
if ( !is_object( cmsms() ) ) exit;

$type_id = $params['type_id'];

if (isset($params['submit']) || isset($params['apply']))
{
  if ( !empty($params['template_id']) )
  {
    // updating a template
    if ( !catTemplateOperations::SaveTemplateChanges($params) )
    {
      $params['err'] = 'ERROR SAVING CHANGES'; // TODO task: add a lang entry
    }
                             
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

  // force a cache clear?
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

if (isset($params['template_id']))
{
  // editing a template  
  $row = catTemplateOperations::GetTemplateFromID($params['template_id']);
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
   

if (isset($type_id)) 
{
  $attrs = array();

  $attrs['fixed'] = catTemplateOperations::GetAttListFromType( $params['type_id']);
  $attrs['user'] =  catTemplateOperations::GetUserAttList( $params['type_id']);

  $image_count = $this->GetPreference('item_image_count', '1');

  if ($image_count > 0)
  {
    $attrs['images'] = array();

    for ($i=1;$i<=$image_count;$i++)
    {
      $currImg = array(
                        'image_url'       => '{$image_' . $i . '_url}',
                        'image_thumb_url' => '{$image_thumb_' . $i . '_url}',
                        'image_src_url'   => '{$src_image_' . $i . '_url}'
                      );
      
      array_push($attrs['images'] , $currImg);
    }

    array_push($attrs['fixed'] , '{$image_url_array}');
    array_push($attrs['fixed'], '{$src_image_url_array}');
    array_push($attrs['fixed'] , '{$image_thumb_url_array}');

  }  

  $file_count = $this->GetPreference('item_file_count', 0);

  if ($file_count > 0)
  {
    $attrs['files'] = array();
    
    for ($i=1;$i<=$file_count;$i++)
    {
      $currFile = array(
                    'filename'  => '{$file_'.$i.'_name}',
                    'url'       => '{$file_'.$i.'_url}',
                    'ext'       => '{$file_'.$i. '_ext}'
                  );
      array_push( $attrs['files'] , $currFile);
    }

    array_push($attrs['fixed'] , '{$file_count}');
    array_push($attrs['fixed'] , '{$file_name_array');
    array_push($attrs['fixed'] , '{$file_url_array}');
    array_push($attrs['fixed'] , '{$file_ext_array}');
    array_push($attrs['fixed'] , '{$root_url}');
    array_push($attrs['fixed'] , '{$image_root}');
  }
  
  if (isset($type_id))
  { 
    switch ($type_id)
    {
      case CTEMPLATE_ITEM:
        $this->smarty->assign('title_template_vars', $this->Lang('title_item_template_vars') );
        $this->smarty->assign_by_ref('avail_attrs', $attrs);
      break;  
      
      case CTEMPLATE_CATEGORY:  
        $this->smarty->assign('title_template_vars', $this->Lang('title_cat_template_vars') );
        $this->smarty->assign_by_ref('avail_attrs', $cattrs);  
      break;
      
      case CTEMPLATE_CATALOG: 
        $this->smarty->assign('title_template_vars', $this->Lang('title_catalog_template_vars') );
        $this->smarty->assign_by_ref('avail_attrs', $pcattrs);    
      break;
      
      case CTEMPLATE_COMPARISON:
        $this->smarty->assign('title_template_vars', $this->Lang('title_compare_template_vars') );
        $this->smarty->assign_by_ref('avail_attrs', $compattrs);   
      break;
      
      case CTEMPLATE_FEATURE:
        $this->smarty->assign('title_template_vars', $this->Lang('title_feature_template_vars') );
        $this->smarty->assign_by_ref('avail_attrs', $feattrs);    
      break;
    }
  }
  else 
  {
    $this->smarty->assign('avail_attrs',$attrs.', '.$cattrs.', '.$feattrs);  
  }     

/*
$attrs = '{$title}, {$notes}, ';
         
$cattrs = '{$title}, {$notes}, {$prev}, {$prevurl}, {$navstr}, {$next}, {$nexturl}, {$items}, ';
          
$pcattrs = '{$items}, {$attrlist}, {$root_url}, {$image_root}, ';
           
$compattrs = '{$items}, {$attrlist}, {$root_url}, {$image_root}';
             
$feattrs = '{$items}, {$root_url}, {$image_root}';
*/




/*
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
*/
        
$this->smarty->assign('startform', $this->CreateFormStart($id, 'edittempl', $returnid));
$this->smarty->assign('endform', $this->CreateFormEnd());
$this->smarty->assign('hidden',$this->CreateInputHidden($id, 'template_id', $templateid));
$this->smarty->assign('title_title',$this->Lang('title_title'));
$this->smarty->assign('title_template',$this->Lang('title_template'));
$this->smarty->assign('title_template_type',$this->Lang('title_template_type'));
$this->smarty->assign('title_avail_attrs',$this->Lang('title_avail_attrs'));
$this->smarty->assign('image_count', $image_count);
$this->smarty->assign('file_count', $file_count);



//    $this->smarty->assign('title_avail_imattrs',$this->Lang('title_avail_imattrs'));
//    $this->smarty->assign_by_ref('avail_imattrs',$imattrs);
$this->smarty->assign('input_template_type',$this->CreateInputDropdown($id, 'type_id', $typeids, -1, isset($type_id)?$type_id:''));

$this->smarty->assign('message',$params['message']);
$this->smarty->assign('input_title',$this->CreateInputText($id, 'title', $title, 20, 255));
$this->smarty->assign('input_template',$this->CreateTextArea(false, $id, $template, 'templ'));

$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', lang('submit')));
$this->smarty->assign('apply', $this->CreateInputSubmit($id, 'apply', lang('apply')));
$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', lang('cancel')));
echo $this->ProcessTemplate('edittemplate.tpl');
?>
