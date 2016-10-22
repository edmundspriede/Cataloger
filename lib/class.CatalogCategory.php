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
class CatalogCategory extends Content
{
  const TAB_CAT_OPTIONS = 'cc_cataloger_options';
  const TAB_CAT_IMAGES = 'cc_cataloger_images';
  private $_mod;
  private $attrs;
  private $_attr_list = array();

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
    return $this->Lang('category_page');
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
    $has_images = (bool)$mod->GetPreference('category_image_count', 0);
    $out = parent::GetTabNames();
    
    if( isset($out[self::TAB_CAT_OPTIONS]) ) 
    {
      $out[self::TAB_CAT_OPTIONS] = $mod->lang('tab_advanced_options');
    }
    
    if( isset($out[self::TAB_CAT_OPTIONS]) ) 
    {
      $out[self::TAB_CAT_OPTIONS] = $mod->lang('tab_advanced_options');
    }
    
    if( isset($out[self::TAB_CAT_IMAGES]) )
    {
      if($has_images)
      {
        $out[self::TAB_CAT_IMAGES] = $mod->lang('tab_images');
      }
       else
      {
        unset($out[self::TAB_CAT_IMAGES]);
      }
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
    
    $this->AddProperty('sub_template',    0, self::TAB_CAT_OPTIONS);
    $this->AddProperty('sort_order',      1, self::TAB_CAT_OPTIONS);
    $this->AddProperty('recurse',         2, self::TAB_CAT_OPTIONS);
    $this->AddProperty('items_per_page',  3, self::TAB_CAT_OPTIONS);
    $this->AddProperty('images',          0, self::TAB_CAT_IMAGES);
    
    $this->getUserAttributes();
    
    #lets add the fields
    if(is_array($this->attrs))
    {
      foreach ($this->attrs as $k => $thisAttr)
      {
        $this->AddProperty($thisAttr->alias, 110 + $thisAttr->field_order, self::TAB_MAIN);
      }
    }
    
  }
  
  function AddProperty($name, $priority, $tab = self::TAB_MAIN, $required = FALSE)
  {
    parent::AddProperty($name, $priority, $tab, $required);
  }
  
  protected function display_single_element($one, $adding)
  {
    $gCms = cmsms();
    $mod = $this->get_mod();
    $smarty = $gCms->GetSmarty();

    if( in_array($one, $this->_attr_list) )
    {
      return catFields::RenderInput( $this->attrs[$one], $this->GetPropertyValue($this->attrs[$one]->attr) );
    } 

    switch($one)
    {
      
      case 'sub_template':
      {
        $subTemplates = catTemplateOperations::GetTemplateListFromID(2);

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
      
      case 'recurse':
          $ret = array($mod->Lang('title_category_recurse'), CatalogerHelper::RenderRecursiveInput( $this->GetPropertyValue('recurse') ) );          
          return $ret;     
      break;
      
      case 'images':
      {
        $ret = array('', CatalogerHelper::RenderPageImageUpLoader($this->mAlias, 'category'));
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
          
      case 'items_per_page':
      {
        $ipp = $this->GetPropertyValue('items_per_page');
        if ($ipp == "" || $ipp == -1)
        {
          $ipp = get_site_preference('Cataloger_mapi_pref_category_items_per_page', '10');
        }
        
        $dpArr =  array(
                          '1'=>'1', 
                          '2'=>'2', 
                          '3'=>'3', 
                          '4'=>'4', 
                          '5'=>'5', 
                          '6'=>'6', 
                          '7'=>'7', 
                          '8'=>'8', 
                          '9'=>'9', 
                          '10'=>'10', 
                          '11'=>'11', 
                          '12'=>'12', 
                          '13'=>'13', 
                          '14'=>'14', 
                          '15'=>'15', 
                          '16'=>'16', 
                          '17'=>'17', 
                          '18'=>'18', 
                          '19'=>'19', 
                          '20'=>'20', 
                          '24'=>'24', 
                          '25'=>'25', 
                          '30'=>'30', 
                          '40'=>'40', 
                          '50'=>'50', 
                          '1000'=>'1000'
                        );
        
        $ret = array(
                      $mod->Lang('title_global_items_per_page2'), 
                      $mod->CreateInputDropdown(
                                                  '', 
                                                  'items_per_page', 
                                                  $dpArr,
                                                  -1, 
                                                  $ipp
                                                )
                      );
        return $ret;
      }
      break;
      
      default:
        return parent::display_single_element($one, $adding);
    }
  }
  
  
  function ValidateData()
  {
    $v = parent::ValidateData();

    if ($v !== FALSE)
    {
      return $v;
    }

    return $this->validation;
  }

  function validationError($msg)
  {
    if (!is_array($this->validation))
    {
      $this->validation = array();
    }

    array_push($this->validation, $msg);
  }

  function FillParams($params, $editing = false)
  {   
    if( !isset($params) ) return;
    
    $config = cmsms()->GetConfig();
    $mod = $this->get_mod();
        
    $parameters = array('sub_template', 'sort_order', 'recurse', 'items_per_page');

    foreach($parameters as $oneparam)
    {
      if (isset($params[$oneparam]))
      {
        $this->SetPropertyValue($oneparam, $params[$oneparam]);
      }       
    }

    $this->getUserAttributes();

    foreach ($this->attrs as $thisAttr)
    {
      if (isset($params[$thisAttr->safe]))
      {
          $this->SetPropertyValue($thisAttr->attr, $params[$thisAttr->safe]);
      }
    }
    
    // Copy and resize the image files...
    $imgcount = $mod->GetPreference('category_image_count', '1');
    $herosize = $mod->GetPreference('category_image_size_hero', '400');
    $thumbsize = $mod->GetPreference('category_image_size_thumbnail', '90');
    
    for ($i=1; $i<= $imgcount; $i++)
    {

      if (isset($_FILES['image' . $i]['size']) && $_FILES['image' . $i]['size'] > 0)
      {

        if (! preg_match('/\.jpg$|\.jpeg$/i', $_FILES['image' . $i]['name']))
        {
          $this->validationError($mod->Lang('badimageformat', $_FILES['image' . $i]['name']));
        }
        else
        {
          $source = $_FILES['image' . $i]['tmp_name'];
          $target = cms_join_path(
                                    $config['uploads_path'],
                                    catFilesOperations::getAssetPath('s'),
                                    $this->mAlias . '_src_' . $i . '.jpg'
                                  );
          $cres = copy($source, $target);

          if (!$cres)
          {
            $this->validationError( $mod->Lang('badimage', $_FILES['image' . $i]['name']) );
          }
        }
      }
    }

    foreach ($params as $thisParam => $thisParamVal)
    {

      if (substr($thisParam, 0, 9) == 'rm_image_')
      {
        $imageSpecParts = explode('_', $thisParam);
        catFilesOperations::purgeAllImage($imageSpecParts[2], $imageSpecParts[3]);
      }
    }
    

    parent::FillParams($params);
  }

  function PopulateParams(&$params)
  { 

    $parameters = array('sub_template', 'sort_order', 'recurse', 'items_per_page');

    # this may be better implemented in validate?  (JM)
    foreach ($parameters as $oneparam)
    {
      $tmp = $this->GetPropertyValue($oneparam);

      if (isset($tmp) && !empty($tmp))
      {
        $params[$oneparam] = $tmp;
      }
    }

    $this->getUserAttributes();

    $safeattrlist = array();

    foreach ($this->attrs as $thisAttr)
    {
      $tmp = $this->GetPropertyValue($thisAttr->attr);

      if (isset($tmp) && $tmp != '')
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
    }
        
    $params['title'] = $this->mName;
    $params['menutext'] = $this->mMenuText;
    $params['template_id'] = $this->mTemplateId;
    $params['alias'] = $this->mAlias;
    $params['parent_id'] = $this->mParentId;
    $params['active'] = $this->mActive;
    $params['showinmenu']=$this->mShowInMenu;
    $params['attrlist'] = $safeattrlist;
    $params['_i_content_id'] = $this->mId; #if needed at all (???????)

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
      catFilesOperations::renameImages($this->mOldAlias, $this->Alias());
    }
  }

  protected function Insert()
  {
    parent::Insert();
    
    if( ($this->mOldAlias != $this->Alias()) && $this->mOldAlias && $this->Alias() )
    {
      catFilesOperations::renameImages($this->mOldAlias,$this->Alias());
    }
  }

  function UpdatePropertyName($propName, $propNewName)
  {
    $tempValue = '';
    
    if ($this->is_known_property($propName))
    {
      $tempValue = $this->GetPropertyValue($propName);
      $this->RemoveProperty($propName, false);   
    }
    
    $this->AddExtraProperty($propName);
    $this->SetPropertyValue($propNewName, $tempValue);
  }   

  function DeleteProperty($propName)
  {
    $this->RemoveProperty($propName, false);    
  }
  /**
  * #
  * 
  * @param mixed $params
  */
  function RenderContent($params)
  {    
    $mod = $this->get_mod();
    
    $smarty = $mod->GetActionTemplateObject();
    
    if( empty($smarty) ) $smarty = cmsms()->GetSmarty();
    
    $cntObj = new stdClass();
  
    $showMissing = '_' . $mod->GetPreference('show_missing', '1');

    foreach ($params as $key => $val)
    {
      $smarty->assign($key, $params[$key]);
      $cntObj->$key = $params[$key];
    }

    list($curPage, $categoryItems) = catContentOperations::getCatalogItemsList($params);

    if (isset($params['sort_order']) && $params['sort_order'] == 'alpha')
    {
      usort( $categoryItems, array('catContentOperations', 'contentalpha') );
    }
    
    $cntObj->items = $categoryItems;
    $cntObj->default_content = $params['content_en'];
    
    $smarty->assign('items', $categoryItems);
    $smarty->assign('main_content', $params['content_en']);
    
    list($nav, $categoryItems) = $this->pagination($categoryItems, $params);
    
    foreach ($nav as $key=>$val)
    {
      $smarty->assign($key, $nav[$key]);
    }
    
    $cntObj->nav = $nav;
    
    $imgcount = $mod->GetPreference('category_image_count', '1');
    $fullSize = $mod->GetPreference('category_image_size_hero', '400');
    $thumbSize = $mod->GetPreference('category_image_size_thumbnail', '90');
    
    $cntObj->imgcount = $imgcount;
    $cntObj->fullSize = $fullSize;
    $cntObj->thumbSize = $thumbSize;
    
    $imageArray = array();
    $srcImgArray = array();
    $thumbArray = array();

    for($i = 1; $i <= $imgcount; $i++)
    {
      $is = catFilesOperations::imageSpec($curPage->Alias(), 'cf', $i, $fullSize);
      array_push( $imageArray, $is);
      $is = catFilesOperations::imageSpec($curPage->Alias(), 'ct', $i, $thumbSize);
      array_push($thumbArray, $is);
      $sis = catFilesOperations::srcImageSpec($curPage->Alias(), $i);  
      array_push($srcImgArray, $sis);

      $is = catFilesOperations::imageSpec($curPage->Alias(), 'cf', $i, $fullSize);                                         
      $smarty->assign('image_' . $i . '_url', $is);
      $sis = catFilesOperations::srcImageSpec($params['alias'], $i);                     
      $smarty->assign('src_image_' . $i . '_url', $sis);
      $is =  catFilesOperations::imageSpec($curPage->Alias(), 'ct', $i, $thumbSize);                     
      $smarty->assign('image_thumb_' . $i . '_url', $is);
    }
    
    $smarty->assign('ctlg', $cntObj); 
    $smarty->assign('image_url_array', $imageArray);
    $smarty->assign('src_image_url_array', $srcImgArray);
    $smarty->assign('image_thumb_url_array', $thumbArray);
    $mod->smartyBasics();

    $cache_id = '_ctg_' . md5( serialize($params) );
    $compile_id = 'Cataloger_' . $this->mId;

    return $smarty->fetch(
                            $mod->GetDatabaseResource('catalog_' . $params['sub_template']),
                            $cache_id,
                            $compile_id
                          );
  }
  
  function pagination($categoryItems, $params)
  {
    $mod = self::get_mod();  
    $count = count($categoryItems);

    if ( isset($_REQUEST['start']) )
    {
      $start = $_REQUEST['start'];
    }
    else
    {
      $start = 0;
    }

    if ( isset($params['items_per_page']) )
    {
      $end = max($params['items_per_page'], 1);
    }
    else
    {
      $end = max($count, 1);
    }

    $thisUrl = $_SERVER['REQUEST_URI'];
    $thisUrl = preg_replace('/(\?)*(\&)*start=\d+/', '', $thisUrl);

    if (strpos($thisUrl, '?') === false)
    {
      $delim = '?';
    }
    else
    {
      $delim = '&';
    }

    $nav = array();

    if ($start > 0)
    {
      $nav['prev'] = '<a href="' . $thisUrl . $delim 
                     . 'start=' . max(0, $start-$end) . '">' 
                     . $this->Lang('prev') . '</a>';

      $nav['prevurl'] =  $thisUrl . $delim . 'start=' . max(0, $start - $end);
    }
    else
    {
      $nav['prev'] = '';
      $nav['prevurl'] = '';
    }

    if ($start + $end < $count)
    {
      $nav['next'] = '<a href="' . $thisUrl . $delim . 'start=' 
                     . ($start + $end) . '">' . $mod->Lang('next') . '</a>';
                              
      $nav['nexturl'] = $thisUrl . $delim . 'start=' . ($start + $end);
    }
    else
    {
      $nav['next'] = '';
      $nav['nexturl'] = '';
    }

    $navstr = '';
    $pageInd = 1;
    
    for ( $i = 0; $i < $count; $i += $end )
    {
      if ($i == $start)
      {
        $navstr .= "<span class=\"p-" . $pageInd . "  nolink\">" . $pageInd . "</span>";
      }
      else
      {
        $navstr .=  '<a href="' . $thisUrl . $delim . 'start='  . $i . '">' . $pageInd . '</a>';
      }

      $navstr .= $mod->Lang('navTab');
      $pageInd++;
    }

    $navstr = rtrim( $navstr, $mod->Lang('navTab') );
    $categoryItems = array_splice($categoryItems, $start, $end);

    if (strlen($navstr) > 1)
    {
      $nav['navstr'] =  $navstr;
      $nav['hasnav'] = true;
    }
    else
    {
      $nav['navstr'] =  '';
      $nav['hasnav'] = false;
    }
    
    $ret = array($nav, $categoryItems);
   
    return $ret;
        
  }

}
?>