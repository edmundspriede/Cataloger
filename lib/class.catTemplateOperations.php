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
/**
* class catTemplate_Operations
* @author Jo Morg (Fernando Morgado)
* @copyright ©2013 Jo Morg
*/
class catTemplateOperations
{
  protected static $_db;
  
  private static function _getDB()
  {
    
    if ( !is_object(self::$_db) )
    {
      self::$_db = cmsms()->GetDb();
    }
    
    return self::$_db;
    
  }
  
  /**
  * Remove all non-word characters
  * 
  * @param mixed $text
  */
  private static function _safeAtt($text)
  {
    return strtolower(preg_replace('/\W/', '', $text)); 
  }
  
  /**
  * returns an array with the current Template types
  * @author JoMorg (Fernando Morgado)
  * @since 0.12
  * @static
  * @returns a ref to an array
  */
  static function &GetCatalogerTemplateTypes()
  {
    $db = self::_getDB();
    $types = array();

    $query = 'SELECT type_id, name FROM ' 
             . cms_db_prefix()
             . 'module_catalog_template_type';
             
    $dbresult = $db->Execute($query);
    
    while ($dbresult !== false && $row = $dbresult->FetchRow())
    {
      $types[$row['name']] = $row['type_id'];
    }
    
    return $types;
  }
  
  /**
  * SaveTemplate...
  * 
  * @param mixed $type_id
  * @param mixed $temp_name
  * @param mixed $template
  */
  static function SaveTemplate($type_id, $temp_name, $template)
  {
    $db = self::_getDB();
    
    $temp_id = $db->GenID(cms_db_prefix() . 'module_catalog_template_seq');

    $query = 'INSERT INTO '
           . cms_db_prefix()
           . 'module_catalog_template (id, type_id, title, template) '
           . ' VALUES (?,?,?,?)';
           
    $arr = array($temp_id, $type_id, $temp_name, $template);
    
    $dbresult = $db->Execute($query, $arr);
    
    return $dbresult;
  }
   
  /**
  * SaveTemplateChanges
  * 
  * @param mixed $params
  * @return Boolean = TRUE if success
  * @since 0.12
  */
  static function SaveTemplateChanges($params)
  {
    $err = FALSE;
    
    if (!is_array($params))
    {
      $errDescrp = 'Saving Template (error: $params not an array)';
      audit($params['template_id'], __METHOD__, $errDescrp);
      $err = TRUE;
      return $err;
    }
    
    $db = self::_getDB();
    
    $query = 'UPDATE '
             . cms_db_prefix()
             . 'module_catalog_template set title=?, template=?, type_id=? WHERE id=?';
    
    $qArr = array($params['title'],
                  $params['templ'],
                  $params['type_id'], 
                  $params['template_id']);
             
    $dbresult = $db->Execute($query, $qArr);
    
    if (!$dbresult)
    {
      $errDescrp = 'Saving Template (error: No results from query)';
      audit($params['template_id'], __METHOD__, $errDescrp);
      $err = TRUE;
    }
    
    return $err;
  }
  
  static function GetTemplateFromID($id)
  {
    $db = self::_getDB();
    
    $query = 'SELECT title, template, type_id, id FROM ' 
             . cms_db_prefix()
             . 'module_catalog_template WHERE id=? ORDER by title';
  
    $dbresult = $db->Execute($query, array($id));
    return $dbresult->FetchRow();
  }
  
  static function &GetTemplateListFromID($id)
  {
    $db = self::_getDB();
    
    $query = 'SELECT title, template, type_id, id FROM ' 
             . cms_db_prefix()
             . 'module_catalog_template WHERE type_id=? ORDER by title';
  
    $dbresult = $db->Execute($query, array($id));
    
    while ($dbresult !== false && $row = $dbresult->FetchRow())
    {
      $subTemplates[$row['title']] = $row['id'];
    }
    
    return $subTemplates;
  }
  
  static function &GetAttsFromType($id)
  {
    $db = self::_getDB();
    
    $query = "SELECT attribute, alias, type_id FROM "
             . cms_db_prefix()
             . "module_catalog_attr WHERE type_id=?";
         
    $dbresult = $db->Execute($query, array($id));

    return $dbresult;
      
  }
  
  
  static function getTemplateFromAlias($alias)
  {
    $db = self::_getDB();
    $query = 'SELECT id from ' . cms_db_prefix() . 'module_catalog_template where title=?';
    $dbresult = $db->Execute($query, array($alias) );
                            
    if ($dbresult !== false && $row = $dbresult->FetchRow())
    {
      return 'catalog_' . $row['id'];
    }
    
      return '';  
  }
  
  
  static function &GetUserAttList($id)
  {
    # for the moment leave at as is but more changes are needed.... (JM)
    $dbresult = self::GetAttsFromType($id);
    
    $attrs = array();

    while ($dbresult !== false && $row = $dbresult->FetchRow())
    {
      $safeattr = self::_safeAtt($row['attribute']);
      
      $attr = array(
                      'name'  => '{$' . $safeattr . '}',
                      'alias' => '{$' . $row['alias'] . '}'
                    );
      
      array_push($attrs, $attr);
    }
    
    return $attrs;
  }
  
  
  static function GetAttListFromType($TemplateType)
  {
    $a = array();
    
    switch ($TemplateType)
    {
      case CTEMPLATE_ITEM:
                
                array_push($a, '{$title}');
                array_push($a, '{$notes}');
                break;
                
      case CTEMPLATE_CATEGORY:
      
                array_push($a, '{$title}');
                array_push($a, '{$notes}');
                array_push($a, '{$prev}');
                array_push($a, '{$prevurl}');
                array_push($a, '{$navstr}');
                array_push($a, '{$next}');
                array_push($a, '{$nexturl}');
                array_push($a, '{$items}');
                break;
       
      case CTEMPLATE_CATALOG:
                                
                array_push($a, '{$items}');
                array_push($a, '{$attrlist}');
                array_push($a, '{$root_url}');
                array_push($a, '{$image_root}');
                break; 
      
      case CTEMPLATE_COMPARISON:
                
                array_push($a, '{$items}');
                array_push($a, '{$attrlist}');
                array_push($a, '{$root_url}');
                array_push($a, '{$image_root}');
                break;  
                
      case CTEMPLATE_FEATURE:

                array_push($a, '{$items}');
                array_push($a, '{$root_url}');
                array_push($a, '{$image_root}');
                break; 
      
    }
    
    #self::GetAttList($a, $TemplateType);

    return $a;
  }
  
  static function importSampleTemplates( $onlytype = 'all' )
  {
    $db = self::_getDB();
    $dir = opendir( dirname(__FILE__) . DIRECTORY_SEPARATOR . 'includes');
    $temps = array();
    
    while( $filespec = readdir($dir) )
    {
      if(! preg_match('/\.tpl$/i', $filespec))
      {
        continue;
      }
      
        array_push($temps, $filespec);
    }
            
    sort($temps);

    foreach ($temps as $filespec)
    {
      $file = file(
                    dirname(__FILE__)
                    . DIRECTORY_SEPARATOR 
                    . 'includes'
                    . DIRECTORY_SEPARATOR
                    . $filespec
                  );
              
      $template = implode('', $file);
      
      $temp_name = preg_replace('/\.tpl$/i', '', $filespec);
      
      #
      // check if it already exists
      $excheck = 'SELECT id from ' . cms_db_prefix() . 'module_catalog_template where title=?';
      $dbcount = $db->Execute($excheck , array($temp_name));
      #
      
      # rename???
      while ($dbcount && $dbcount->RecordCount() > 0)
      {
        $temp_name .= '_new';
        $dbcount = $db->Execute( $excheck, array($temp_name) );
      }
      
      $type_id = -1;
      $type = substr( $temp_name, 0, strpos($temp_name, '-') );
      
      if ( $type == 'Item' && ($onlytype=='all' || $onlytype='Item') )
      {
        $type_id = CTEMPLATE_ITEM;
      }
      else if ( $type == 'Category' && ($onlytype=='all' || $onlytype='Category') )
      {
        $type_id = CTEMPLATE_CATEGORY;
      }
      else if ( $type == 'Printable' && ($onlytype=='all' || $onlytype='Printable') )
      {
        $type_id = CTEMPLATE_CATALOG;
      }
      else if ( $type == 'Comparison' && ($onlytype=='all' || $onlytype='Comparison') )
      {
        $type_id = CTEMPLATE_COMPARISON;
      }
      else if ( $type == 'Feature' && ($onlytype=='all' || $onlytype='Feature') )
      {
        $type_id = CTEMPLATE_FEATURE;
      }
      
      catTemplate_Operations::SaveTemplate($type_id, $temp_name, $template);
      
      # 
      /*   
      $temp_id = $db->GenID(cms_db_prefix() . 'module_catalog_template_seq');

      $query = 'INSERT INTO '
             . cms_db_prefix()
             . 'module_catalog_template (id, type_id, title, template) '
             . ' VALUES (?,?,?,?)';
             
      $arr = array($temp_id,$type_id, $temp_name,$template);
      
      $dbresult = $db->Execute($query, $arr);
      */
      #
                              
      $this->SetTemplate( 'catalog_' . $temp_id, $template);
    }
  
  }
 
}
?>