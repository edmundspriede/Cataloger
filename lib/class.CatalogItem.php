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
class CatalogItem extends Content
{
  const TAB_ITEM_OPTIONS  = 'cc_cataloger_options';
  const TAB_ITEM_IMAGES   = 'cc_cataloger_images';
  const TAB_ITEM_FILES    = 'cc_cataloger_files';
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
    return $this->Lang('item_page');
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
    $has_images = (bool)$mod->GetPreference('item_image_count', 0);
    $has_files = (bool)$mod->GetPreference('item_file_count', 0); 
    
    $out = parent::GetTabNames();
    
    if( isset($out[self::TAB_ITEM_OPTIONS]) ) 
    {
      $out[self::TAB_ITEM_OPTIONS] = $mod->lang('tab_advanced_options');
    }
    
    if( isset($out[self::TAB_ITEM_IMAGES]) )
    {
      if($has_images)
      {
        $out[self::TAB_ITEM_IMAGES] = $mod->lang('tab_images');
      }
       else
      {
        unset($out[self::TAB_ITEM_IMAGES]);
      }
    }
    
    if( $out[self::TAB_ITEM_FILES] )
    {
      if($has_files)
      {
        $out[self::TAB_ITEM_FILES] = $mod->lang('tab_files');
      }
       else
      {
        unset($out[self::TAB_ITEM_FILES]);
      }
    }

    return $out;
  }
  
  function HasAttributes()
  {
    return is_array($this->attrs) && (count($this->attrs) > 0);
  }

  function getUserAttributes()
  {
    if( empty($this->attrs) ) 
    {
      $this->attrs = catUserDefAttributesOps::getUserAttributes('catalog_attrs');
      $this->_attr_list = array_keys($this->attrs); 
    } 
  }
  
  function getAttrs()
  {
    $this->getUserAttributes();
    return $this->attrs;
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
    $this->AddProperty('sub_template',    0, self::TAB_ITEM_OPTIONS);
    $this->AddProperty('images',          0, self::TAB_ITEM_IMAGES);
    $this->AddProperty('files',           0, self::TAB_ITEM_FILES);
    
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
  
  protected function display_single_element($one, $adding = false)
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
      $subTemplates = catTemplateOperations::GetTemplateListFromID(1);

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
    
    case 'images':
    {
      
      if ($this->mAlias == '')
      {
        $ret = array('new page message | Images');  
      }
      else
      {
        $ret = array('', CatalogerHelper::RenderPageImageUpLoader($this->mAlias));
      }
      
      return $ret;
    }
    break;
        
    case 'files':
    {
      
      if ($this->mAlias == '')
      {
        $ret = array('new page message | Files');  
      }
      else
      {
        $ret = array('', CatalogerHelper::RenderPageFileUpLoader($this->mAlias));
      }
      
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

    $this->validation = FALSE;
    
    $parameters = array('sub_template');

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
      
    $mod = $this->get_mod();

    // Copy the image files...
    $imgcount = $mod->GetPreference('item_image_count', '2');
    $herosize = $mod->GetPreference('item_image_size_hero', '400');
    $thumbsize = $mod->GetPreference('item_image_size_thumbnail', '70');
    $catalogsize = $mod->GetPreference('item_image_size_catalog', '100');
    $categorysize = $mod->GetPreference('item_image_size_category', '70');

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
        $mod->purgeAllImage($imageSpecParts[2], $imageSpecParts[3]);
      }
    }
    
    // and uploaded files
    $filecount = $mod->GetPreference('item_file_count', 0);
    $typelist = $mod->GetPreference('item_file_types', 'pdf,swf,flv,doc,odt,ods,xls');
    $types = explode(',' , $typelist);

    if ($filecount > 0)
    {
      $dirspec = cms_join_path(
                                $config['uploads_path'],
                                catFilesOperations::getAssetPath('f'),
                                $this->mAlias 
                              );

      if (!is_dir($dirspec))
      {
        mkdir($dirspec);
      }

      for ($i=0; $i< $filecount; $i++)
      {

        if (isset($_FILES['file' . $i]['size']) && $_FILES['file' . $i]['size'] > 0)
        {
          $tspec = preg_replace('/[^\w\d\.\-_]+/', '_', $_FILES['file' . $i]['name']);
          // keep original image
          $extension = substr($tspec, strrpos($tspec, '.') + 1);

          if (!empty($extension) && in_array($extension, $types))
          {
            $mod->Audit(0, $mod->Lang('friendlyname'), $mod->Lang('uploaded', array($tspec, $this->mAlias)));
            
            $target = cms_join_path($dirspec, $tspec);
            $cres = copy($_FILES['file' . $i]['tmp_name'], $target);

            if (!$cres)
            {
              $this->validationError($mod->Lang('badimage', $_FILES['image' . $i]['name']));
            }
          }
          else
          {
            $mod->Audit(0, $mod->Lang('friendlyname'), $mod->Lang('badfile', $tspec));
            $this->validationError($mod->Lang('badfile', $tspec));
          }
        }
      }
    }

    foreach ($params as $thisParam => $thisParamVal)
    {

      if (substr($thisParam, 0, 8) == 'rm_file_')
      {
        $pSpec = preg_replace('/\.\.|\//', '', $thisParamVal);

        $spec = cms_join_path(
                                $config['uploads_path'],
                                catFilesOperations::getAssetPath('f'),
                                $this->mAlias,
                                $pSpec
                              );
        unlink($spec);
      }
    }

    parent::FillParams($params);
  }

  function PopulateParams(&$params)
  {
    $parameters = array('sub_template');

    foreach ($parameters as $oneparam)
    {
      $tmp = $this->GetPropertyValue($oneparam);

      if (isset($tmp) && ! empty($tmp))
      {
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
      return $this->RenderContent($params); 
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
      cms_utils::get_module('Cataloger')->renameImages($this->mOldAlias,$this->Alias());
    }
    
  }
  
  function AddProperty($name, $priority, $tab = self::TAB_MAIN, $required = FALSE)
  {
    parent::AddProperty($name, $priority, $tab, $required);
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

  /**
  * #
  * 
  * @param mixed $params
  */
  function RenderContent($params)
  {
    $mod = $this->get_mod();
    //$smarty = $mod->GetActionTemplateObject();
    $config = cmsms()->GetConfig();
    
    if( empty($smarty) ) $smarty = cmsms()->GetSmarty();
    $cntObj = new stdClass();
    
    $showMissing = '_' . $mod->GetPreference('show_missing', '1');

    $imageArray     = array();
    $fileArray      = array();
    $fileUrlArray   = array();
    $fileTypeArray  = array();
    $srcImgArray    = array();
    $thumbArray     = array();
    
    $imgcount = $mod->GetPreference('item_image_count', 2);
    $filecount = $mod->GetPreference('item_file_count', 0);
    $fullSize = $mod->GetPreference('item_image_size_hero', '400');
    $thumbSize = $mod->GetPreference('item_image_size_thumbnail', '70');
    $prunelist = ($mod->GetPreference('show_extant', '1') == '1');
    
    $actualfilecount = 0;

    foreach ($params as $key => $val)
    {
      $smarty->assign($key, $params[$key]);
      $cntObj->$key = $params[$key];
    }
    
    $cntObj->default_content = $params['content_en'];
    $smarty->assign('main_content', $params['content_en']);
    
    if ($imgcount > 0)
    {
      $smarty->assign('ImageCount', $imgcount); # new in 1.12 (JM)
      $cntObj->ImageCount = $imgcount;          # new in 1.12 (JM)

      for ($i = 1 ; $i <= $imgcount ; $i++)
      {
        
        if (! $prunelist || catFilesOperations::srcExists($this->mAlias, $i))
        {
          $propvalue = catFilesOperations::imageSpec($this->mAlias, 'f', $i, $fullSize);
          $propName = 'image_' . $i . '_url';
          $cntObj->$propName = $propvalue;
          $smarty->assign($propName, $propvalue);
          array_push($imageArray, $propvalue);

          $propvalue = catFilesOperations::imageSpec($this->mAlias, 't', $i, $thumbSize);
          $propName = 'image_thumb_' . $i . '_url';
          $cntObj->$propName = $propvalue;
          $smarty->assign($propName, $propvalue);
          array_push($thumbArray, $propvalue);
          
          $propvalue = catFilesOperations::srcImageSpec($this->mAlias, $i);
          $propName = 'src_image_' . $i . '_url';
          $cntObj->$propName = $propvalue;
          $smarty->assign($propName, $propvalue);
          array_push($srcImgArray, $propvalue);          
        }
                  
      }
          
    }
    
    if ($filecount > 0)
    {
      list($fileArray, $fileTypeArray) = catFilesOperations::getFiles($this->mAlias);

      foreach ($fileArray as $i => $v)
      {   
        $propvalue = $config['uploads_url']
                     . '/' 
                     . catFilesOperations::getAssetPath('f')
                     . '/'
                     . $this->mAlias
                     . '/'
                     . $fileArray[$i];

        $propName = 'file_' . ($i + 1) . '_url';
        $cntObj->$propName = $propvalue;
        $smarty->assign($propName, $propvalue);
        array_push($fileUrlArray, $propvalue);
        
        $propvalue = $fileArray[$i];
        $propName = 'file_' . ($i + 1) . '_name';
        $cntObj->$propName = $propvalue;           
        $smarty->assign($propName, $propvalue); 
               
        $propvalue = $fileTypeArray[$i];
        $propName = 'file_' . ($i + 1) . '_ext';
        $cntObj->$propName = $propvalue;           
        $smarty->assign($propName, $propvalue);

        $actualfilecount += 1;
      }
    }  
    
    $cntObj->attrlist = $params['attrlist'];
    $cntObj->image_url_array = $imageArray;
    $cntObj->file_url_array = $fileUrlArray;
    $cntObj->file_name_array = $fileArray;
    $cntObj->file_ext_array = $fileTypeArray;
    $cntObj->file_count = $filecount;
    $cntObj->image_thumb_url_array = $thumbArray;
    $cntObj->src_image_url_array = $srcImgArray;
        
    $smarty->assign('attrlist', $params['attrlist']);
    $smarty->assign('image_url_array', $imageArray);
    $smarty->assign('file_url_array', $fileUrlArray);
    $smarty->assign('file_name_array', $fileArray);
    $smarty->assign('file_ext_array', $fileTypeArray);
    $smarty->assign('file_count', $filecount);
    $smarty->assign('image_thumb_url_array', $thumbArray);
    $smarty->assign('src_image_url_array', $srcImgArray);
    $mod->smartyBasics();
    

    $smarty->assign('ctlg', $cntObj);

    $cache_id = '_itm_' . md5( serialize($params) );
    $compile_id = 'Cataloger_' . $this->mId;

    return $smarty->fetch(
                            $mod->GetDatabaseResource('catalog_' . $params['sub_template']),
                            $cache_id,
                            $compile_id
                          );
  }

}
?>
