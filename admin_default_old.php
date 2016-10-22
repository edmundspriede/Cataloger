<?php
if ( !is_object( cmsms() ) ) exit;

//Load the shows
$entryarray = array();

$query = "SELECT t.id, t.title, tt.name as type FROM "
         . cms_db_prefix()
         . "module_catalog_template t, "
         . cms_db_prefix()
         . "module_catalog_template_type tt WHERE t.type_id = tt.type_id ORDER by t.type_id, title";
      
$dbresult = $db->Execute($query);

$rowclass = 'row1';

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
                                      array('template_id'=>$row['id'])
                                    );
  $onerow->rowclass = $rowclass;
  
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
                                          array('template_id'=>$row['id'])
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

  array_push($entryarray, $onerow);

  ($rowclass == "row1" ? $rowclass="row2" : $rowclass="row1"); # toggle css classes
}

$this->smarty->assign('items', $entryarray);
$this->smarty->assign('itemcount', count($entryarray));
$this->smarty->assign('category', $this->Lang('templatelist'));
$this->smarty->assign('title_template', $this->Lang('title_template'));
$this->smarty->assign('title_template_type', $this->Lang('title_template_type'));
$this->smarty->assign('notemplates', $this->Lang('notemplates'));
$this->smarty->assign('message', isset($params['message'])?$params['message']:'');
$this->smarty->assign('section', $this->Lang('subtemplates'));

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
                          

$this->smarty->assign('addlink', $link);

#Display template
echo $this->ProcessTemplate('templatelist.tpl');
?>
