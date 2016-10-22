<?php
class catFilesOperations
{
  protected static $_initialized = FALSE;
  protected static $_mod;
  protected static $_cms;
  protected static $_config;
  
  private static function _initialize()
  {
    if(!self::$_initialized)
    {
      self::$_mod = cms_utils::get_module('Cataloger');
      self::$_cms = cmsms();
      self::$_config = self::$_cms->GetConfig();
    }
  }
  
  // type can be "s" - source image, "i" - processed image, or "f" - file
  static function getAssetPath( $type = "i", $default = false )
  {
    self::_initialize();
    $mod = self::$_mod;
    $config = self::$_config;
    $rootPath = $config['root_path'];
   
    $uploadbase = str_replace(['uploads_path'], $rootPath, '');
    
    if($default)
    {
      switch($type)
      {
        case 's': $ret = cms_join_path($uploadbase, 'images', 'catalog_src'); break; 
        case 'i': $ret = cms_join_path($uploadbase, 'images', 'catalog'); break; 
        case 'f': $ret = cms_join_path($uploadbase, 'catalogerfiles'); break; 
      }
      
      # just in case there are mismatched DIRECTORY SEPARATOR chars
      $ret = str_replace('/', DIRECTORY_SEPARATOR, $ret);
      $ret = str_replace('\\', DIRECTORY_SEPARATOR, $ret);
      
      return trim($ret, DIRECTORY_SEPARATOR);
    }
      
    switch ($type)
    {
      case 's':
      {
        $_ByDefault = cms_join_path($uploadbase, 'images', 'catalog_src');
        $ret = $mod->GetPreference('image_upload_path', $_ByDefault); 
      }
      break; 

      
      case 'i':
      {
        $_ByDefault = cms_join_path($uploadbase, 'images', 'catalog');
        $ret = $mod->GetPreference('image_proc_path', $_ByDefault);  
      }
      break; 
      
      case 'f':
      {
        $_ByDefault = cms_join_path($uploadbase, 'catalogerfiles');
        $ret = $mod->GetPreference('file_upload_path', $_ByDefault);  
      }
      break; 
      
    }

    # just in case there are mismatched DIRECTORY SEPARATOR chars
    $ret = str_replace('/', DIRECTORY_SEPARATOR, $ret);
    $ret = str_replace('\\', DIRECTORY_SEPARATOR, $ret);
    
    return trim($ret, DIRECTORY_SEPARATOR);   
  }
  
  static function Extender()
  {
    $ret = '&amp;ac=';
    
    for ($r = 0; $r < 5; $r++)
    {
      $ret .= rand(0,9);
    }
    
    return $ret;
  }


  static function imageSpec($alias, $type, $image_number, $size, $anticache=true, $forceshowmissing=false)
  {
    self::_initialize();
    $mod = self::$_mod;
    $config = self::$_config;
    
    $contentops = self::$_cms->GetContentOperations();
    $returnid = $contentops->GetDefaultContent();
    
    $pretty_url = 'cataloger_image/';
    $url = $mod->create_url('cntnt01', 'image', $returnid, array(), FALSE, FALSE, $pretty_url);
    
    if ($mod->showMissing == '')
    {
      $mod->showMissing = $mod->GetPreference('show_missing', '1');
    }
    
    $ext = '';
    
    if ($anticache)
    {
      $ext = self::Extender();
    }
    
    $r = $url
         . ( ($config['url_rewriting'] == 'mod_rewrite') ? '?i=' : '&amp;i=' ) 
         . $alias
         . '_'
         . $type
         . '_'
         . $image_number
         . '_'
         . $size
         . ($forceshowmissing ? '_1' : '_' . $mod->showMissing)
         . '.jpg'
         . $ext;

      return $r;
  }

  static function srcImageSpec($alias, $image_number)
  {
    self::_initialize();
    $mod = self::$_mod;
    
    if ($mod->showMissing == '')
    {
      $mod->showMissing = $mod->GetPreference('show_missing', '1');
    }

    if( !self::srcExists($alias, $image_number) )
    {
      if ($mod->showMissing != '1')
      {
        return $mod->GetRootURL()
               . '/modules/Cataloger/images/trans.gif';
      }
      else
      {
        return $mod->GetRootURL()
               . '/modules/Cataloger/images/no-image.gif';
      }
    }
    else
    {
      return $mod->GetUploadsURL()
             . self::getAssetPath('s')
             . '/'
             . $alias 
             . '_src_'
             . $image_number
             . '.jpg';
    }
  }

  static function srcExists($alias, $image_number)
  {
    self::_initialize();
    $mod = self::$_mod;
    $config = self::$_config;
    
    $srcSpec = cms_join_path(
                              $config['uploads_path'],
                              self::getAssetPath('s'),
                              $alias . '_src_' . $image_number . '.jpg'
                            );
    
    return file_exists($srcSpec);
  }


  static function getFiles($alias)
  {
    self::_initialize();
    $mod = self::$_mod;
    $config = self::$_config;
    
    $dirspec = cms_join_path(
                              $config['uploads_path'],
                              self::getAssetPath('f'),
                              $alias
                             );
  
    $files = array();
    $types = array();
    
    if (is_dir($dirspec))
    {  
      $dh  = opendir($dirspec);
      
      while ( false !== ( $filename = readdir($dh) ) )
      {
        
        if ($filename != '.' && $filename != '..')
        {
          $files[] = $filename;
          
          if (strpos($filename, '.') !== false)
          {
            $types[] = substr($filename, strrpos($filename, '.') + 1);
          }
          else
          {
            $types[] = '?';
          }
          
        }
        
      }
    }
    
    return array($files,$types);
  }

  static function purgeAllImage($alias, $imageNumber)
  {   
    self::purgeScaledImages($alias, $imageNumber);
    self::purgeSourceImage($alias, $imageNumber);        
  }
  
  static function purgeScaledImages($alias, $imageNumber)
  {
    self::_initialize();
    $mod = self::$_mod;
    $config = self::$_config;
    $assets_path = self::getAssetPath('f');
    
    $srcDir = cms_join_path(
                              $config['uploads_path'],
                              $assets_path
                            );
    
    $toDel = array();
    
    if ($dh = opendir($srcDir))
    {
    
      while (($file = readdir($dh)) !== false)
      {
        $fileParts = explode('_', $file);
        
        if ($fileParts[0] == $alias && $fileParts[2] == $imageNumber)
        {
          array_push($toDel, $srcDir . '/' . $file);
        }
        
      }
      
      closedir($dh);
    }
        
    foreach ($toDel as $thisDel)
    {
      unlink($thisDel);
    }
  }

  static function purgeSourceImage($alias, $imageNumber)
  {
    self::_initialize();
    $mod = self::$_mod;
    $config = self::$_config;
    
    $fn = $alias 
         . '_src_'
         . $imageNumber
         . '.jpg';
        
    $srcSpec =  cms_join_path(
                                $config['uploads_path'],
                                self::getAssetPath('s'),
                                $fn 
                              );
                              
    unlink($srcSpec);
    
  }


  static function renameImages($old, $newAlias)
  {
    self::_initialize();
    $mod = self::$_mod;
    $config = self::$_config;
    
    $path = cms_join_path(
                            $config['uploads_path'],
                            self::getAssetPath('s')
                          );
    
    
    if ($handle = opendir($path) )
    {
      
      while ( false !== ( $file = readdir($handle )) )
      {
        if ( substr($file, 0, strlen($old) ) == $old)
        {
          $newspec = $newAlias . substr( $file, strlen($old) );
          
          $old_file = cms_join_path($path, $file);
          $new_file = cms_join_path($path, $newspec);
          
          rename($old_file, $new_file);
        }
      }
      
      closedir($handle);
    }
  }
  
  ###### generic.....
  
  /**
  * Delete a file or recursively delete a directory
  *
  * @param string $str Path to file or directory
  */
  static function recursiveDelete($str)
  {
    $Result = is_file($str);
    
    if($Result)
    {
      $Result = @unlink($str);
    }
    else
    {
      $Result = is_dir($str);
      
      if($Result)
      {
        $scan = glob( rtrim($str, '/') . '/*' );
        
        foreach($scan as $index=>$path)
        {
          $Result = $Result && recursiveDelete($path);
        }
        
        $Result = $Result && @rmdir($str);     
      }
    }
    
    return $Result;
  }
  
  /**
  * @method DeleteFile
  * Just a convinience function to silently delete files
  * May fail on some enviroments
  * 
  * @param mixed $filename
  * @returns True if success, otherwise false
  */
  static function DeleteFile($filename)
  {
    $result = file_exists($filename);
    if ($result) $result = @unlink($filename);
    return $result;
  }
  
  static function DeleteFileWMask($mask)
  {
    array_walk(glob($mask), 'unlink');
  }

}
?>
