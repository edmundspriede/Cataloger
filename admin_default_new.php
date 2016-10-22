<?php
if ( !is_object( cmsms() ) ) exit;

$this->initAdminNav($id, $params, $returnid);

# now for the templates

# Items 

$ItemPageArray = array();
 
$query = "SELECT id, title FROM "
         . cms_db_prefix()
         . "module_catalog_template WHERE type_id = 1 ORDER by title";
      
$dbresult = $db->Execute($query);

while ($dbresult !== false && $row = $dbresult->FetchRow())
{
  $onerow = new stdClass();

  $onerow->id = $row['id'];
  $onerow->type = $row['type'];
  $onerow->title = $this->CreateLink(
                                      $id, 
                                      'edittempl', 
                                      $returnid,
                                      $row['title'], 
                                      array('template_id' => $row['id'], 'type_id' => 1)
                                    );
  
  $Icon = $gCms->variables['admintheme']->DisplayImage(
                                                        'icons/system/edit.gif',
                                                        $this->Lang('edit'),
                                                        '',
                                                        '',
                                                        'systemicon'
                                                       );

  $onerow->editlink = $this->CreateLink(
                                          $id, 
                                          'edittempl', 
                                          $returnid,
                                          $Icon,
                                          array('template_id' => $row['id'], 'type_id' => 1)
                                        );
                                        
  $Icon = $gCms->variables['admintheme']->DisplayImage(
                                                        'icons/system/delete.gif',
                                                        $this->Lang('delete'),
                                                        '',
                                                        '',
                                                        'systemicon'
                                                       );
      
  $onerow->deletelink = $this->CreateLink(
                                            $id, 
                                            'deletetempl', 
                                            $returnid,
                                            $Icon,
                                            array('template_id' => $row['id']), 
                                            $this->Lang('areyousure','Template')
                                          );
                                          
  array_push($ItemPageArray, $onerow);

}


# Categories

$CategoryPageArray = array();
 
$query = "SELECT id, title FROM "
         . cms_db_prefix()
         . "module_catalog_template WHERE type_id = 2 ORDER by title";
      
$dbresult = $db->Execute($query);

while ($dbresult !== false && $row = $dbresult->FetchRow())
{
  $onerow = new stdClass();

  $onerow->id = $row['id'];
  $onerow->type = $row['type'];
  $onerow->title = $this->CreateLink(
                                      $id, 
                                      'edittempl', 
                                      $returnid,
                                      $row['title'], 
                                      array('template_id' => $row['id'], 'type_id' => 2)
                                    );
  
  $Icon = $gCms->variables['admintheme']->DisplayImage(
                                                        'icons/system/edit.gif',
                                                        $this->Lang('edit'),
                                                        '',
                                                        '',
                                                        'systemicon'
                                                       );

  $onerow->editlink = $this->CreateLink(
                                          $id, 
                                          'edittempl', 
                                          $returnid,
                                          $Icon,
                                          array('template_id' => $row['id'], 'type_id' => 2)
                                        );
                                        
  $Icon = $gCms->variables['admintheme']->DisplayImage(
                                                        'icons/system/delete.gif',
                                                        $this->Lang('delete'),
                                                        '',
                                                        '',
                                                        'systemicon'
                                                       );
      
  $onerow->deletelink = $this->CreateLink(
                                            $id, 
                                            'deletetempl', 
                                            $returnid,
                                            $Icon,
                                            array('template_id'=>$row['id']), 
                                            $this->Lang('areyousure','Template')
                                          );
                                          
  array_push($CategoryPageArray, $onerow);

}

# Printable Catalog

$PrintableCatalogArray = array();
 
$query = "SELECT id, title FROM "
         . cms_db_prefix()
         . "module_catalog_template WHERE type_id = 3 ORDER by title";
      
$dbresult = $db->Execute($query);

while ($dbresult !== false && $row = $dbresult->FetchRow())
{
  $onerow = new stdClass();

  $onerow->id = $row['id'];
  $onerow->type = $row['type'];
  $onerow->title = $this->CreateLink(
                                      $id, 
                                      'edittempl', 
                                      $returnid,
                                      $row['title'], 
                                      array('template_id' => $row['id'], 'type_id' => 3)
                                    );
  
  $Icon = $gCms->variables['admintheme']->DisplayImage(
                                                        'icons/system/edit.gif',
                                                        $this->Lang('edit'),
                                                        '',
                                                        '',
                                                        'systemicon'
                                                       );

  $onerow->editlink = $this->CreateLink(
                                          $id, 
                                          'edittempl', 
                                          $returnid,
                                          $Icon,
                                          array('template_id' => $row['id'], 'type_id' => 3)
                                        );
                                        
  $Icon = $gCms->variables['admintheme']->DisplayImage(
                                                        'icons/system/delete.gif',
                                                        $this->Lang('delete'),
                                                        '',
                                                        '',
                                                        'systemicon'
                                                       );
      
  $onerow->deletelink = $this->CreateLink(
                                            $id,  
                                            'deletetempl', 
                                            $returnid,
                                            $Icon,
                                            array('template_id'=>$row['id']), 
                                            $this->Lang('areyousure','Template')
                                          );
                                          
  array_push($PrintableCatalogArray, $onerow);

}


# Item Comparison

$ItemComparisonArray = array();
 
$query = "SELECT id, title FROM "
         . cms_db_prefix()
         . "module_catalog_template WHERE type_id = 4 ORDER by title";
      
$dbresult = $db->Execute($query);

while ($dbresult !== false && $row = $dbresult->FetchRow())
{
  $onerow = new stdClass();

  $onerow->id = $row['id'];
  $onerow->type = $row['type'];
  $onerow->title = $this->CreateLink(
                                      $id,  
                                      'edittempl', 
                                      $returnid,
                                      $row['title'], 
                                      array('template_id' => $row['id'], 'type_id' => 4)
                                    );
  
  $Icon = $gCms->variables['admintheme']->DisplayImage(
                                                        'icons/system/edit.gif',
                                                        $this->Lang('edit'),
                                                        '',
                                                        '',
                                                        'systemicon'
                                                       );

  $onerow->editlink = $this->CreateLink(
                                          $id, 
                                          'edittempl', 
                                          $returnid,
                                          $Icon,
                                          array('template_id' => $row['id'], 'type_id' => 4)
                                        );
                                        
  $Icon = $gCms->variables['admintheme']->DisplayImage(
                                                        'icons/system/delete.gif',
                                                        $this->Lang('delete'),
                                                        '',
                                                        '',
                                                        'systemicon'
                                                       );
      
  $onerow->deletelink = $this->CreateLink(
                                            $id, 
                                            'deletetempl', 
                                            $returnid,
                                            $Icon,
                                            array('template_id'=>$row['id']), 
                                            $this->Lang('areyousure','Template')
                                          );
                                          
  array_push($ItemComparisonArray, $onerow);

}

# Feature List

$FeatureListArray = array();
 
$query = "SELECT id, title FROM "
         . cms_db_prefix()
         . "module_catalog_template WHERE type_id = 5 ORDER by title";
      
$dbresult = $db->Execute($query);

while ($dbresult !== false && $row = $dbresult->FetchRow())
{
  $onerow = new stdClass();

  $onerow->id = $row['id'];
  $onerow->type = $row['type'];
  $onerow->title = $this->CreateLink(
                                      $id, 
                                      'edittempl', 
                                      $returnid,
                                      $row['title'], 
                                      array('template_id' => $row['id'], 'type_id' => 5)
                                    );
  
  $Icon = $gCms->variables['admintheme']->DisplayImage(
                                                        'icons/system/edit.gif',
                                                        $this->Lang('edit'),
                                                        '',
                                                        '',
                                                        'systemicon'
                                                       );

  $onerow->editlink = $this->CreateLink(
                                          $id,
                                          'edittempl', 
                                          $returnid,
                                          $Icon,
                                          array('template_id' => $row['id'], 'type_id' => 5)
                                        );
                                        
  $Icon = $gCms->variables['admintheme']->DisplayImage(
                                                        'icons/system/delete.gif',
                                                        $this->Lang('delete'),
                                                        '',
                                                        '',
                                                        'systemicon'
                                                       );
      
  $onerow->deletelink = $this->CreateLink(
                                            $id,  
                                            'deletetempl', 
                                            $returnid,
                                            $Icon,
                                            array('template_id'=>$row['id']), 
                                            $this->Lang('areyousure','Template')
                                          );
                                          
  array_push($FeatureListArray, $onerow);

}

$current_tab = isset($params['tab']) ? $params['tab'] : 'item_page_templates';

/**
* Smarty stuff
*/

$tab_headers = $this->StartTabHeaders()
               . $this->SetTabHeader('item_page_templates',         $this->Lang('title_item_page_templates_tab'), $current_tab == 'item_page_templates')
               . $this->SetTabHeader('category_page_templates',     $this->Lang('title_category_page_templates_tab'), $current_tab == 'category_page_templates')
               . $this->SetTabHeader('printable_catalog_templates', $this->Lang('title_printable_catalog_templates_tab'), $current_tab == 'printable_catalog_templates')
               . $this->SetTabHeader('item_comparison_templates',   $this->Lang('title_item_comparison_templates_tab'), $current_tab == 'item_comparison_templates')
               . $this->SetTabHeader('feature_list_templates',      $this->Lang('title_feature_list_templates_tab'), $current_tab == 'feature_list_templates')
               . $this->EndTabHeaders()
               . $this->StartTabContent();

$smarty->assign('tab_headers', $tab_headers);
                      
$smarty->assign('end_tab', $this->EndTab());

$smarty->assign('tab_footers', $this->EndTabContent());

$smarty->assign('start_item_page_templates_tab', $this->StartTab('item_page_templates'));
$smarty->assign('start_category_page_templates_tab', $this->StartTab('category_page_templates'));
$smarty->assign('start_printable_catalog_templates_tab', $this->StartTab('printable_catalog_templates'));
$smarty->assign('start_item_comparison_templates_tab', $this->StartTab('item_comparison_templates'));
$smarty->assign('start_feature_list_templates_tab', $this->StartTab('feature_list_templates'));

$smarty->assign('PageItems', $ItemPageArray);
$smarty->assign('PageCategories', $CategoryPageArray);
$smarty->assign('PrintableCatalogs', $PrintableCatalogArray);
$smarty->assign('ItemComparisons', $ItemComparisonArray);
$smarty->assign('FeatureLists', $FeatureListArray);

// Content defines and Form stuff for the admin
$smarty->assign('start_form', $this->CreateFormStart($id, 'save_admin_prefs', $returnid));
$smarty->assign('input_allow_add',$this->CreateInputCheckbox($id, 'allow_add', 1,
$this->GetPreference('allow_add','0')). $this->Lang('title_allow_add_help'));
$smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', lang('submit')));

// translated strings
$smarty->assign('title_allow_add',$this->Lang('title_allow_add'));
$smarty->assign('welcome_text',$this->Lang('welcome_text'));###
$smarty->assign('message',isset($params['message'])?$params['message']:'');
$smarty->assign('section',$this->Lang('subtemplates'));

$icon = $gCms->variables['admintheme']->DisplayImage(
                                                      'icons/system/newobject.gif',
                                                      $this->Lang('addtemplate'),
                                                      '',
                                                      '',
                                                      'systemicon'
                                                     );

$link = $this->CreateLink(
                            $id, 
                            'edittempl', 
                            $returnid, 
                            $icon, 
                            array(),
                            '', 
                            false, 
                            false, ''
                          );
$link .= ' ';

$link .= $this->CreateLink($id, 'edittempl', $returnid,
              $this->Lang('addtemplate'), array(), '', false, false, 'class="pageoptions"');

$link .= '&nbsp;&nbsp;';

$icon = '<img src="'
        . $gCms->config['root_url']
        . '/modules/Cataloger/images/reload.gif" class="systemicon" alt="'
        . $this->Lang('reimporttemplates')
        . '"  title="'
        . $this->Lang('reimporttemplates')
        . '" />';

$link .= $this->CreateLink(
                            $id, 
                            'reimport', 
                            $returnid,
                            $icon, 
                            array(), 
                            '', 
                            false, 
                            false, 
                            ''
                           );

$link .= ' ';

$link .= $this->CreateLink(
                            $id, 
                            'reimport', 
                            $returnid,
                            $this->Lang('reimporttemplates'), 
                            array(), 
                            '', 
                            false, 
                            false, 
                            'class="pageoptions"'
                          );
                          

$smarty->assign('addlink', $link);


$smarty->assign('title_template',$this->Lang('title_template'));
$smarty->assign('title_template_type',$this->Lang('title_template_type'));
$smarty->assign('notemplates',$this->Lang('notemplates'));

// TODO 0 -o JoMorg -c bugfix task: differentiate addlink for each type of sub-template
echo $this->ProcessTemplate('adminpanel.tpl');
?>
