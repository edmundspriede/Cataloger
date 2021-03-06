<?php
if(!defined('CMS_VERSION')) exit;
if(!$this->CheckAccess()) exit;

$this->initAdminNav($id, $params, $returnid);
$this->smarty->assign('startform', $this->CreateFormStart($id, 'submitattrs', $returnid));
$this->smarty->assign('endform', $this->CreateFormEnd());
$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', 'Submit'));
$attributes     = array();
$query          = "SELECT id, attribute, alias, defaultval, type_id, length, order_by, field_type, select_values FROM " . cms_db_prefix() . "module_catalog_attr ORDER BY order_by ASC";
$dbresult       = $db->Execute($query);

$countbytype    = array();
$countbytype[1] = 0;
$countbytype[2] = 0;
$countbytype[3] = 0;

while($dbresult !== false && $row = $dbresult->FetchRow())
{
  $onerow                = new stdClass();
  $onerow->input         = $this->CreateInputText($id, 'attr_' . $row['type_id'] . '_' . $countbytype[$row['type_id']], $row['attribute'], 15, 100);
  $onerow->defaultinput  = $this->CreateInputText($id, 'default_' . $row['type_id'] . '_' . $countbytype[$row['type_id']], $row['defaultval'], 15, 1024);
  $onerow->leninput      = $this->CreateInputText($id, 'len_' . $row['type_id'] . '_' . $countbytype[$row['type_id']], (!empty($row['length']) ? $row['length'] : 25), 2, 4);
  $onerow->aliasinput    = $this->CreateInputText($id, 'alias_' . $row['type_id'] . '_' . $countbytype[$row['type_id']], $row['alias'], 15, 255);
  $onerow->hidden        = $this->CreateInputHidden($id, 'old_' . $row['type_id'] . '_' . $countbytype[$row['type_id']], $row['id']);
  $onerow->type          = $row['type_id'];
  $onerow->field_type    = $this->CreateInputDropdown($id, 'field_type_' . $row['type_id'] . '_' . $countbytype[$row['type_id']], array_flip($this->getFieldTypes()), - 1, $row['field_type']);
  $onerow->select_values = $this->CreateInputText($id, 'select_values_' . $row['type_id'] . '_' . $countbytype[$row['type_id']], $row['select_values'], 15, 255);
  $onerow->order_by      = $row['order_by'];
  $onerow->cbt           = $countbytype[$row['type_id']];
  $onerow->delete        = $this->CreateInputCheckbox($id, 'delete_' . $row['type_id'] . '_' . $countbytype[$row['type_id']], 1, 0);
  $countbytype[$row['type_id']]++;
  array_push($attributes, $onerow);
}

for($i = 0; $i < 3; $i++)
{
  for($j = 1; $j < 4; $j++)
  {
    $onerow                = new stdClass();
    $onerow->input         = $this->CreateInputText($id, 'attr_' . $j . '_' . $countbytype[$j], '', 15, 255);
    $onerow->aliasinput    = $this->CreateInputText($id, 'alias_' . $j . '_' . $countbytype[$j], '', 15, 255);
    $onerow->defaultinput  = $this->CreateInputText($id, 'default_' . $j . '_' . $countbytype[$j], '', 15, 1024);
    $onerow->leninput      = $this->CreateInputText($id, 'len_' . $j . '_' . $countbytype[$j], 25, 2, 4);
    $onerow->field_type    = $this->CreateInputDropdown($id, 'field_type_' . $j . '_' . $countbytype[$j], array_flip($this->getFieldTypes()), - 1, $row['field_type']);
    $onerow->select_values = $this->CreateInputText($id, 'select_values_' . $j . '_' . $countbytype[$j], '', 15, 255);
    $onerow->delete        = '';
    $onerow->hidden        = '';
    $onerow->type          = $j;
    $onerow->order_by      = $countbytype[$j] + 1;
    $onerow->cbt           = $countbytype[$j]++;
    array_push($attributes, $onerow);
  }
}

for($i = 1; $i < 4; $i++)
{
  $sc = array();
  for($j = 0; $j <= $countbytype[$i]; $j++)
  {
    $sc[$j] = $j;
  }
  
  for($k = 0; $k < count($attributes); $k++)
  {
    if($attributes[$k]->type == $i)
    {
      $attributes[$k]->order_sel = $this->CreateInputDropdown($id, 'orderby_' . $i . '_' . $attributes[$k]->cbt, $sc, - 1, $attributes[$k]->order_by);
    }
    $attributes[$k]->istext = FALSE;
  }
}

//debug_display($attributes);
$this->smarty->assign('tab_headers', $this->StartTabHeaders() . $this->SetTabHeader('item', $this->Lang('title_item_tab')) . $this->SetTabHeader('category', $this->Lang('title_category_tab')) . $this->SetTabHeader('catalog', $this->Lang('title_printable_tab')) . $this->EndTabHeaders() . $this->StartTabContent());
$this->smarty->assign('end_tab', $this->EndTab());
$this->smarty->assign('tab_footers', $this->EndTabContent());
$this->smarty->assign('start_item_tab', $this->StartTab('item'));
$this->smarty->assign('start_category_tab', $this->StartTab('category'));
$this->smarty->assign('start_catalog_tab', $this->StartTab('catalog'));
$this->smarty->assign('message', isset($params['message']) ? $params['message'] : '');
$this->smarty->assign('attribute_inputs', $attributes);
$this->smarty->assign('title_item_attributes', $this->Lang('title_item_tab'));
$this->smarty->assign('title_attr_alias', $this->Lang('title_attr_alias'));
$this->smarty->assign('title_attr_length', $this->Lang('title_attr_length'));
$this->smarty->assign('title_attr_length_help', $this->Lang('title_attr_length_help'));
$this->smarty->assign('title_attr_default', $this->Lang('title_attr_default'));
$this->smarty->assign('title_attr_order_by', $this->Lang('title_attr_order_by'));
$this->smarty->assign('title_catalog_attributes', $this->Lang('title_printable_tab'));
$this->smarty->assign('title_category_attributes', $this->Lang('title_category_tab'));
$this->smarty->assign('title_item_attributes_help', $this->Lang('title_item_attributes_help'));
$this->smarty->assign('title_catalog_attributes_help', $this->Lang('title_catalog_attributes_help'));
$this->smarty->assign('title_category_attributes_help', $this->Lang('title_category_attributes_help'));
$this->smarty->assign('title_select_values', $this->Lang('title_select_values'));
$this->smarty->assign('title_field_type', $this->Lang('title_field_type'));
$this->smarty->assign('title_delete', $this->Lang('title_delete'));
$this->smarty->assign('category', $this->Lang('manageattrs'));
// Display template
echo $this->ProcessTemplate('adminattrs.tpl');
?>
