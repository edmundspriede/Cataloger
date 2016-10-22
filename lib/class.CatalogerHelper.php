<?php
#-------------------------------------------------------------------------
# Module: Cataloger - build a catalog or portfolio of stuff
# Version: 0.12
#
# Copyright (c) 2012-2013, Fernando Morgado (JoMorg) jomorg.morg@gmail.com
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
/**
* A helper Class for the Cataloger Module
* @version 1.0
* @author Jo Morg (Fernando Morgado)
*/
class CatalogerHelper
{
  static private $_initialized = FALSE;
  static private $_cms;
  static private $_smarty;
  static private $_config;
  static private $_mod;
  
  /**
  * A few basics
  */
  
  private static function _initialize()
  {
    if(!self::$_initialized)
    {
      self::$_cms = cmsms();
      self::$_config = self::$_cms->GetConfig();
      self::$_smarty = self::$_cms->GetSmarty();
      self::$_mod = self::$_cms->GetModuleInstance('Cataloger');
      self::$_initialized = TRUE;  
    }
  }
  
  #deprecated
  static function get_mod()
  {
    self::_initialize();
    return self::$_mod;
  }

  #### Admin UI helper methods
  static private function _admImageUploaderTableHeader()
  {
    if (!isset($html))
    {
      $mod = self::get_mod();
      $html = '';
      $html .= "\n\n<table class='pagetable'>\n";
      $html .= "<thead>";
      $html .= "\n<tr>\n<th style=\"vertical-align:center;text-align:right;\">#</th>\n<th>";
      $html .= $mod->lang('nameimages'); 
      $html .= "</th>\n<th style=\"vertical-align:center;text-align:center;\">\n";
      $html .= $mod->lang('title_upload');
      $html .= "</th>\n<th class=\"pageicon\">\n";
      $html .= $mod->lang('deleteimage');
      $html .= "</th>\n</tr>\n";
      $html .= '</thead>';
      $html .= "\n";
    }
    
    return $html;
  }
  
  static private function _admFileUploaderTableHeader()
  {
    if (!isset($html))
    {
      $mod = self::get_mod();
      $html = '';
      $html .= "\n\n<table class='pagetable'>\n";
      $html .= "<thead>";
      $html .= "\n<tr>\n<th style=\"vertical-align:center;text-align:center;\">#</th>\n";
      $html .= "\n<th style=\"vertical-align:center;text-align:center;\">Icon</th>\n";
      $html .= "<th style=\"vertical-align:center;text-align:left;\">\n"; 
      $html .= $mod->lang('namefiles');  
      $html .= "</th>\n";  
      $html .= "<th style=\"vertical-align:center;text-align:left;\">\n";
      $html .= $mod->lang('title_upload');
      $html .= "</th>\n<th class=\"pageicon\">\n";
      $html .= $mod->lang('deletefile'); 
      $html .= "</th>\n</tr>\n";
      $html .= '</thead>';
      $html .= "\n";
    }
    
    return $html;
  }
  
  static function &RenderPageImageUpLoader($alias, $type = 'item')
  {
    if ($type == 'category'){$t = '_ct_';} 
    if ($type == 'item'){$t = '_it_';} 
    
    $mod = self::get_mod();
    $imgcount = $mod->GetPreference('category_image_count', '1');
    $thumbsize = $mod->GetPreference('category_image_size_thumbnail', '90');
    $RootURL = $mod->GetPreference('RootURL', cmsms()->config['root_url']);
    
    if ($imgcount != 0)
    {
      $html = self::_admImageUploaderTableHeader(); 
      $html .= "<tbody>\n";
      
      $imgsrc = '';
      $urlext = $mod->create_url( 'm1_', 'image', null, array() );
            
      for ($i = 1 ; $i <= $imgcount ; $i++)
      {
        $imgsrc .=  '<tr><td style="vertical-align:center;text-align:right;">'
                   . sprintf("%03d", $i)
                   . '</td><td style="vertical-align:center;text-align:center;">';
                  
        $tmptxt = '<img alt="'
                   . $mod->lang('nameimages')
                   . '" title="'
                   . $mod->lang('nameimages')
                   . '" src="'
                   //. $RootURL
                   . $urlext . '&i='
                   . $alias
                   . $t
                   . $i
                   . '_'
                   . $thumbsize
                   . '_1.jpg&ac='
                   . rand(0,9)
                   . rand(0,9)
                   . rand(0,9)
                   . '" />';
                   
        $imgsrc .= str_replace('&amp;', '&', $tmptxt);

        $imgsrc .= '</td><td style="vertical-align:center;"><input type="file" name="image'
                   . $i
                   . '" />';
                   
        $imgsrc .= '</td><td style="vertical-align:center;text-align:right"><input type="checkbox" id="rm_image_' 
                   . $alias
                   . '_'
                   . $i
                   . '" name="rm_image_'
                   . $alias
                   . '_'
                   . $i
                   . '" />';
                   
        $imgsrc .= '</td></tr>';
      }
       
        $html .= $imgsrc; 
        
        $html .= "<tbody>\n</table>\n";

      }
      else
      {

        $html .= '<div class="pagetext">';
        $html .= '<img alt="';
        $html .=  $mod->lang('nameimages');
        $html .=  '" title="';
        $html .=  $mod->lang('nameimages');
        $html .=  '" src=" ';
        $html .=  $RootURL;
        $html .=  '/modules/Cataloger/images/no-image.gif" />';
        $html .=  '</div>';


      }
      
      return $html;
  }
  
  static function &RenderPageFileUpLoader($alias)
  {
    $mod = self::get_mod();
    $filecount = $mod->GetPreference('item_file_count', 0);
    
    if ($filecount == 0) return ''; # we shouldn't even get here but return empty nevertheless....

    list($filelist, $filetype) = catFilesOperations::getFiles($alias);
    
    $html = self::_admFileUploaderTableHeader(); 
    $html .= "<tbody>\n<tr>\n";
    
    $filecount = $mod->GetPreference('item_file_count', 0);

    for ($i = 0; $i < $filecount; $i++)
    {
       $filesrc = '';
    
      $filesrc .= '<td style="vertical-align:center;text-align:right;">'
                  . sprintf("%03d", $i)
                  . '</td>'; 

      if (isset($filelist[$i]))
      {
         
        $typeimg = '<img src="'
                   . cmsms()->config['root_url']
                   . '/modules/FileManager/icons/themes/default/extensions/16px/'
                   . $filetype[$i]
                   . '.png" />';
        
        $currfile = $filelist[$i];
        
        $deleteHTML = '<input type="checkbox"" id="rm_file_'
                      . $alias
                      . '_'
                      . $i
                      . '" name="rm_file_'
                      . $filelist[$i]
                      . '" value="'
                      . $filelist[$i]
                      . '"/>';
      }
      else
      {
        $typeimg = '<img src="'
                   . cmsms()->config['root_url']
                   . '/modules/FileManager/icons/themes/default/extensions/16px/0.png" />';
                   
        $currfile = $mod->lang('namefile')
                   . ' # '
                   . ($i + 1);
                   
        $deleteHTML = '&nbsp';
      }
      
      $filesrc .= '<td style="vertical-align:center;text-align:center;">'
                  .  $typeimg
                  . '</td>';
                  
      
      $filesrc .= '<td style="vertical-align:center;text-align:left;">'
                  .  $currfile
                  . '</td>';
      
      $filesrc .= '<td style="vertical-align:middle;">&nbsp;<input type="file" name="file'
                  . $i
                  . '" />';
                        
      $filesrc .= '<td style="vertical-align:middle;text-align:right;">'
                  . $deleteHTML
                  . '</td>';
                  

      $html .= $filesrc;

      $html .= "</td>\n</tr>\n";
    }

    $html .= "<tbody>\n</table>\n";

    return $html;
    
  }
  
  static function RenderRecursiveInput($mode = 'items_one')
  {
    $mod = self::get_mod();
    
    $ret = '<fieldset>'; 
    $ret .= '<legend>' . $mod->lang('title_global_category_recurse2') . '</legend>'; 
    $ret .= "<table>\n"; 
    $ret .= "<tr>\n"; 
    $ret .= "<td>\n"; 
    $ret .= '<input type="radio" name="recurse" value="items_all" '; 
    $ret .= ( ($mode == 'items_all') ? 'checked="checked"' : '' ); 
    $ret .= '/>&nbsp;' . $mod->lang('title_category_recurse_items_all'); 
    $ret .= '</td>'; 
    $ret .= '</tr>'; 
    $ret .= '<tr>'; 
    $ret .= '<td>';
    $ret .= '<input type="radio" name="recurse" value="items_one" ';
    $ret .= ( ($mode == 'items_one') ? 'checked="checked"' : '');
    $ret .= '/>&nbsp;' . $mod->lang('title_category_recurse_items_one') . '</td></tr>';
    $ret .= '<tr><td><input type="radio" name="recurse" value="categories_all" ';
    $ret .= ( ( $mode == 'categories_all' ) ? 'checked="checked"' : '' );
    $ret .= '/>&nbsp;'.$mod->lang('title_category_recurse_categories_all') . '</td></tr>';
    $ret .= '<tr><td><input type="radio" name="recurse" value="categories_one" ';
    $ret .= ( ($mode == 'categories_one') ? 'checked="checked"' :'' );
    $ret .= '/>&nbsp;'.$mod->lang('title_category_recurse_categories_one') . '</td></tr>';
    $ret .= '<tr><td><input type="radio" name="recurse" value="mixed_all" ';
    $ret .= ( ($mode == 'mixed_all') ? 'checked="checked"' : '');
    $ret .= '/>&nbsp;'.$mod->lang('title_category_recurse_mixed_all').'</td></tr>';
    $ret .= '<tr><td><input type="radio" name="recurse" value="mixed_one" ';
    $ret .= ( ($mode == 'mixed_one') ? 'checked="checked"' : '' );
    $ret .= '/>&nbsp;' . $mod->lang('title_category_recurse_mixed_one') . '</td></tr></table>';
    $ret .= '</fieldset>';
    
    return $ret;
  }
  
  ### convenience methods - update module ###
  
  /**
  * A few methods to clean up a bit the Cataloger upgrade method....
  * @author Jo Morg
  * @since Cataloger 1.12
  */
  
  static function Update056()
  {
    $db = cmsms()->GetDb();
    
    $sqlarray = $dict->AddColumnSQL(
                                      cms_db_prefix() 
                                      . "module_catalog_attr",
                                      "is_textarea I"
                                    );
    
    $dict->ExecuteSQLArray($sqlarray);
    
    $query = 'INSERT INTO '
             . cms_db_prefix()
             . 'module_catalog_attr (id,type_id,is_textarea,attribute) VALUES (?,?,?,?)';
    
    $new_id = $db->GenID(cms_db_prefix() . 'module_catalog_attr_seq');
    $dbresult = $db->Execute($query,array($new_id, 1, 1, 'notes'));

    $new_id = $db->GenID(cms_db_prefix().'module_catalog_attr_seq');
    $dbresult = $db->Execute($query,array($new_id, 2, 1, 'notes'));

    $new_id = $db->GenID(cms_db_prefix().'module_catalog_attr_seq');
    $dbresult = $db->Execute($query,array($new_id, 3, 1, 'notes')); 
  }
  
  static function render_admin_nav($id, &$params, $returnid)
  {
    $config = self::$_config;
    $theme = cms_utils::get_theme_object();
    
    $link_a_icon = $this->CreateLink(
                                      $id, 
                                      'defaultadmin', 
                                      $returnid, 
                                      $theme->DisplayImage(
                                                            'icons/topfiles/template.gif', 
                                                            $this->Lang('listtempl'), 
                                                            '', '', 
                                                            'systemicon'
                                                          ),
                                      array() 
                                    );
    
    $link_a = $this->CreateLink(
                                  $id, 
                                  'defaultadmin', 
                                  $returnid, 
                                  $this->Lang('listtempl'), 
                                  array() 
                                );
                                      
    $link_b_icon = $this->CreateLink(
                                      $id, 
                                      'adminattrs', 
                                      $returnid, 
                                      $theme->DisplayImage(
                                                            'icons/topfiles/images.gif', 
                                                            $this->Lang('manageattrs'), 
                                                            '', '', 
                                                            'systemicon'
                                                          ), 
                                      array()
                                    );
                                      
    
    $link_b = $this->CreateLink(
                                  $id, 
                                  'adminattrs', 
                                  $returnid, 
                                  $this->Lang('manageattrs'), 
                                  array() 
                                );
                                
    $link_c_icon = $this->CreateLink($id, 'globalops', $returnid, '<img class="systemicon" alt="'.$this->Lang('globalops').'" title="'.$this->Lang('globalops').'" src="'.$config['root_url'].'/modules/Cataloger/images/global.gif" />');
    $link_c = $this->CreateLink($id, 'globalops', $returnid, $this->Lang('globalops'), array() ).' : '.$this->CreateLink($id, 'adminprefs', $returnid, $theme->DisplayImage('icons/topfiles/siteprefs.gif', $this->Lang('manageprefs'), '', '', 'systemicon'), array() ).$this->CreateLink($id, 'adminprefs', $returnid, $this->Lang('manageprefs'), array() );
                                      
    $nav = $link_a_icon . $link_a . ' : ' . $link_b_icon . $link_b . ' : '  . $link_c_icon . $link_c;
                                
    self::$_smarty->assign('innernav', $nav);
  }

  /**
  * A few methods to clean up a bit the Cataloger upgrade method....
  * END
  */
  
}
?>
