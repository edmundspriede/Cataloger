<?php
#-------------------------------------------------------------------------
# Module: Cataloger - build a catalog or portfolio of stuff
# Version: 0.12
#
# Copyright (c) 2012, Fernando Morgado (JoMorg) jomorg.morg@gmail.com
# Copyright (c) 2006, Samuel Goldstein <sjg@cmsmodules.com>
# For Information, Support, Bug Reports, etc, please visit the
# CMS Made Simple Forge at http://dev.cmsmadesimple.org
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------
class CatalogPrintable extends Content
{
  const TAB_PRINT_OPTIONS     = 'cc_cataloger_options';
  const TAB_PRINT_FIELDSLIST  = 'cc_cataloger_fieldslist';
  
  private $_mod;
  private $attrs;
  private $_attr_list = array();
  protected $validation = FALSE;
    
  
  function __construct()
  {
    parent::__construct();
    $this->mCachable = false;
  }
  
  function ModuleName()
  {
    return 'Cataloger';
  } 

  function IsCopyable()
  {
    return true;
  }
  
  function IsDefaultPossible()
  {
    return FALSE;
  } 
  
  function FriendlyName()
  {
    return $this->Lang('catalog_printable');
  }
  
  function Lang($key)
  {
    return $this->get_mod()->Lang($key);
  }
        
  function get_lang($key)
  {
    return $this->get_mod()->Lang($key);
  }
    
  function get_mod()
  {
    if( is_null($this->_mod) )
    {
      $this->_mod = cms_utils::get_module('Cataloger');
    }
    return $this->_mod;
  }

  public function GetTabNames()
  {
    $mod = $this->get_mod();
    $out = parent::GetTabNames();
    
    if( isset($out[self::TAB_PRINT_OPTIONS]) ) 
    {
      $out[self::TAB_PRINT_OPTIONS] = $mod->lang('tab_advanced_options');
    }
        
    if( isset($out[self::TAB_PRINT_FIELDSLIST]) ) 
    {
      $out[self::TAB_PRINT_FIELDSLIST] = $mod->lang('tab_fields_list');
    }

    return $out;
  }
  
   function HasAttributes()
  {
    return is_array($this->attrs) && (count($this->attrs) > 0);
  }
  
  function getAttrs()
  {
    $this->getUserAttributes();
    return $this->attrs;
  }
  
  function getUserAttributes()
  {
    if( empty($this->attrs) ) 
    {
      $this->attrs = catUserDefAttributesOps::getUserAttributes('catalog_cat_attrs');
      $this->_attr_list = array_keys($this->attrs); 
    } 
  }
  
  // to revisit... copied from feu_protected_page
  // its just a hack to avoid caching issues... but not the best
  public function GetModifiedDate()
  {
    // on frontend requests this will force the template to be recompiled
    // and therefore evaluation to be done for each request.
    if( cmsms()->is_frontend_request() ) return time();
    return parent::GetModifiedDate();
  }
  
  function SetProperties()
  {
    parent::SetProperties();
    $this->AddProperty('sub_template',    0, self::TAB_PRINT_OPTIONS);
    $this->AddProperty('sort_order',      1, self::TAB_PRINT_OPTIONS);
    $this->AddProperty('fieldlist',       0, self::TAB_PRINT_FIELDSLIST);
    #Turn on preview
    $this->mPreview = true;
    #Turn off caching
    $this->mCachable = false;
  }
  
  protected function display_single_element($one, $adding = false)
  {
    $gCms = cmsms();
    $mod = $this->get_mod();
    $smarty = $gCms->GetSmarty();

    switch($one)
    {
      
    case 'fieldlist':
    {
      $item = new CatalogItem();
      $itemAttrs = $item->getAttrs();
      $attrPick = '';
      $selAttrs = explode( ',', $this->GetPropertyValue('fieldlist') );
      
      foreach ($itemAttrs as $thisAttr)
      {
        $attrPick .= '<input type="checkbox" name="fieldlist[]" value="' . $thisAttr->attr . '" ';
        
        if ( in_array($thisAttr->attr, $selAttrs) )
        {
          $attrPick .= ' checked="checked"';
        }
        
        $attrPick .= ' />&nbsp;' . $thisAttr->attr . '<br />';
      }

      $ret = array($this->Lang('which_attributes'), $attrPick);
       
       return $ret;
    }
             
    case 'sub_template':
    {
      $subTemplates = catTemplateOperations::GetTemplateListFromID(3);

      $ret = array(
                    '<label for="in_sub_template">' 
                    . $mod->lang('Sub') 
                    . ' '
                    . lang('template') 
                    . ':</label>',
                    $mod->CreateInputDropdown(
                                                '', 
                                                'sub_template', 
                                                $subTemplates, 
                                                -1, 
                                                $this->GetPropertyValue('sub_template')
                                              )
                  );
       
       return $ret;
    }
    break;

    case 'sort_order':
    {
      $ret = array();
      
      $so = $this->GetPropertyValue('sort_order');

      if ($so == '')
      {
          $so = $mod->GetPreference('category_sort_order', 'natural');
      }
                                                          
      $ret = array(
                    $mod->Lang('title_global_item_sort_order2'), 
                    $mod->CreateInputDropdown(
                                                '',
                                                'sort_order', 
                                                array(
                                                        $mod->Lang('natural_order')=>'natural',                                                                $mod->Lang('alpha_order')=>'alpha'
                                                      ), 
                                                      -1, 
                                                      $so
                                              )
                   );
      return $ret;
      
    }
    break;    

    default:
      return parent::display_single_element($one, $adding);
    }
  }
  

  function FillParams($params, $editing = true)
  { 
    if( !isset($params) ) return;

    $this->mCachable = false;

    $parameters = array('sub_template', 'sort_order');

    foreach ($parameters as $oneparam)
    {
      if (isset($params[$oneparam]))
      {
        $this->SetPropertyValue($oneparam, $params[$oneparam]);
      }
    }

    if(isset($params['fieldlist']))
    {
      if (! is_array($params['fieldlist']))
      {
        $params['fieldlist'] = array($params['fieldlist']);
      }

      $this->SetPropertyValue('fieldlist', implode(',', $params['fieldlist']));
    }
    else
    {
      $this->SetPropertyValue('fieldlist', '');
    }

		parent::FillParams($params);
	}

  function PopulateParams(&$params)
  {   
    $parameters = array('sub_template', 'sort_order', 'fieldlist');

    foreach($parameters as $oneparam)
    {
      $tmp = $this->GetPropertyValue($oneparam);
      
      if (isset($tmp) && !empty($tmp))
      {
        if($oneparam  == 'fieldlist')
        {
           $params[$oneparam] = $tmp;
           continue;
        }
        
        $params[$oneparam] = $tmp;
      }
    }
		
    $this->getUserAttributes();
    $safeattrlist = array();

    foreach ($this->attrs as $thisAttr)
    {
      $tmp = $this->GetPropertyValue($thisAttr->attr);
      
      if (isset($tmp) && $tmp!='')
      {
        $params[$thisAttr->safe] = $tmp;
        
        if (isset($thisAttr->alias) && $thisAttr->alias!='')
        {
          $params[$thisAttr->alias] = $tmp;
        }
        
        $thisSafeAttr = array();
        $thisSafeAttr['name'] = ucfirst($thisAttr->attr);
        $thisSafeAttr['key'] = '{$' . $thisAttr->safe . '}';
        
        if ($thisAttr->alias != '')
        {
          $thisSafeAttr['aliaskey'] = '{$' . $thisAttr->alias . '}';
        }
        array_push($safeattrlist, $thisSafeAttr);
      }
      else
      {
        $params[$thisAttr->safe] = '';
        $params[$thisAttr->alias] = '';			
      }
    }
    
    $params['title'] = $this->mName;
    $params['menutext'] = $this->mMenuText;
    $params['template_id'] = $this->mTemplateId;
    $params['alias'] = $this->mAlias;
    $params['parent_id'] = $this->mParentId;
    $params['active'] = $this->mActive;
    $params['showinmenu']=$this->mShowInMenu;
    $params['attrlist'] = $safeattrlist; 
  }
  
  function Show($param = '')
  {
    $param = trim($param);
    
    if( !$param ) $param = 'content_en';
    $param = str_replace(' ','_',$param); 
    
    if ($param == 'content_en')
    {
      $this->PopulateParams($params);
      $params['content_en'] = parent::Show($param);
      return $this->RenderContent($params);;  
    }
    else
    {
      return parent::Show($param);
    }
  }

  protected function Update()
  {
    parent::Update();
    
    if( ($this->mOldAlias != $this->Alias()) && $this->mOldAlias && $this->Alias() )
    {
      cms_utils::get_module('Cataloger')->renameImages($this->mOldAlias,$this->Alias());
    }
  }

  protected function Insert()
  {
    parent::Insert();
    
    if( ($this->mOldAlias != $this->Alias()) && $this->mOldAlias && $this->Alias() )
    {
      catFilesOperations::renameImages( $this->mOldAlias, $this->Alias() );
    }
  }
   
  function UpdatePropertyName($propName, $propNewName)
  {
     $tempValue = '';
     if ($this->is_known_property($propName))
     {
          $tempValue = $this->GetPropertyValue($propName);
          $this->RemoveProperty($propName,false);   
     }
     $this->AddExtraProperty($propName);
     $this->SetPropertyValue($propNewName, $tempValue);  
  }   

  function DeleteProperty($propName)
  {
    $this->RemoveProperty($propName,false);       
  }

  function RenderContent($params)
  {
    $mod = $this->get_mod();
    $smarty = cmsms()->GetSmarty();
    $cntObj = new stdClass();
    
    $showMissing = '_' . $mod->GetPreference('show_missing', '1');
    $params['alias'] = '/';           # ????
    $params['recurse'] = 'items_all'; # ????

    foreach ($params as $key=>$val)
    {
      $smarty->assign($key, $params[$key]);
      $cntObj->$key = $params[$key];
    }
    
    $cntObj->default_content = $params['content_en'];
    $smarty->assign('main_content', $params['content_en']);

    list($curPage, $pageItems) = catContentOperations::getCatalogItemsList($params);

    if (isset($params['sort_order']) && $params['sort_order'] == 'alpha')
    {
      usort( $pageItems, array("catContentOperations", "contentalpha") );
    }

    $count = count($pageItems);
    
    $fl = array();
    
    if( isset($params['fieldlist']) )
    {
      $fl = explode(',', $params['fieldlist']);
    }
     
    $fldlist = array();

    foreach($fl as $tk => $tv)
    {
      $attr = strtolower(preg_replace('/\W/', '', $tv));
      $fldlist[$attr] = $tv;
    }
    
    $cntObj->FieldList = $fldlist;
    $cntObj->attrlist = $fldlist;
    $cntObj->Items = $pageItems;
    //debug_display($pageItems);
    $smarty->assign('items', $pageItems);
    $fullSize = $mod->GetPreference('item_image_size_catalog', '100');
    
    
    $imageArray = array();
    //$fileArray = array();
    //$fileUrlArray = array();
    //$fileTypeArray = array();
    $srcImgArray = array();
    $thumbArray = array();
    
    $imageArray = array();
    $srcImgArray = array();
    $_images = array();
    
    $imgcount = $mod->GetPreference('item_image_count', 2);
    $filecount = $mod->GetPreference('item_file_count', 0);
    $fullSize = $mod->GetPreference('item_image_size_hero', '400');
    $thumbSize = $mod->GetPreference('item_image_size_thumbnail', '70');
    $prunelist = ($mod->GetPreference('show_extant', '1') == '1');
    
    for ($i = 1 ; $i <= $imgcount ; $i++)
    {
      $_url = catFilesOperations::imageSpec($curPage->Alias(), 'ctf', $i, $fullSize);
      $_src_url = catFilesOperations::srcImageSpec($curPage->Alias(), $i);
      array_push($imageArray, $_url);
      array_push($srcImgArray, $_src_url);
      $_images[$i]['url'] = $_url;
      $_images[$i]['src_url'] = $_src_url;
      $smarty->assign('image_' . $i . '_url', $_url);
      $smarty->assign('src_image_'.$i.'_url', $_src_url);
      #$smarty->assign('src_image_'.$i.'_url', catFilesOperations::srcImageSpec($params['alias'], $i)); wtf????
    }

    $cntObj->ImagesArray = $_images;
    $cntObj->image_url_array = $imageArray;
    $cntObj->image_thumb_url_array = $thumbArray;
    
    $smarty->assign('attrlist', $fldlist);

    $smarty->assign('image_url_array', $imageArray);
    $smarty->assign('src_image_url_array', $srcImgArray);
    $smarty->assign('image_thumb_url_array', $thumbArray);
    $mod->smartyBasics();
    
    #####################################################################
//    $cntObj->default_content = $params['_default_cnt'];
//    $cntObj->main_content = $params['_default_cnt'];
//    $smarty->assign('main_content', $params['_default_cnt']);
    ##################################################################### 

    $smarty->assign('ctlg', $cntObj);    
                     
    $cache_id = '_prt_' . md5( serialize($params) );
    $compile_id = 'Cataloger_' . $this->mId;
    
    return $smarty->fetch(
                            $mod->GetDatabaseResource('catalog_' . $params['sub_template']),
                            $cache_id,
                            $compile_id
                          );
  }
}
?>
