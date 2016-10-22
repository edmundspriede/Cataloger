<?php
class catContentOperations
{
  private static $_cms;
  private static $_mod;
  private static $_db;
  private static $_initialized = FALSE;
  
  private static function _initialize()
  {
    if(!self::$_initialized)
    {
      self::$_cms = cmsms();
      self::$_mod = self::$_cms->GetModuleInstance('Cataloger');
      self::$_db = self::$_cms->GetDb();
      self::$_initialized = TRUE; 
    } 
  }
  
  # deprecated
  private static function _getMod()
  {
    self::_initialize();
    return self::$_mod; 
  }  
  
  #deprecated
  private static function _getDB()
  {
    self::_initialize();
    return self::$_db;    
  }
   
  static function &GetContentFromParams(&$params)
  {
    $gCms= cmsms();
    $mod = self::_getMod();
    $vars = $mod->GetVariables(); # need a new way to get this....
    $hm = $gCms->GetHierarchyManager();
    
    if ( isset($params['alias']) && $params['alias'] == '/' )
    { # alias is root (/)
      $content = $hm->getFlatList();
      $curHierDepth = isset($params['start_depth']) ? $params['start_depth'] : -1;
      $curHierarchy = '';
      $curHierLen = 0;
      $curPage = new Content();
    }
    else
    { # either alias is NOT set or not root or neither
       if (isset($params['content_id']))
       { # we have a content_id
          $curPageID = $vars[$params['content_id']];
          $curPageNode = $hm->sureGetNodeById($curPageID);
          $curPage = $curPageNode->GetContent();
       }
       elseif (isset($params['alias']))
       { # we have an alias
          $curPageNode = $hm->sureGetNodeByAlias($params['alias']);
          $curPage = $curPageNode->GetContent();
          $curPageID = $curPage->Id();
       }
       else
       { 
          $ci = $gCms->get_content_id(); 
         if( isset($ci) )
         { # no id, no alias: get content_id from CMSMS application variables : deprecated in core.....
            $curPageID = $ci;
            $curPageNode = $hm->sureGetNodeById($curPageID);
            $curPage = $curPageNode->GetContent();
         }
       }  
      
       $curHierarchy = $curPage->Hierarchy();
       $curHierLen = strlen($curHierarchy);
       $curHierDepth = substr_count($curHierarchy, '.');     
       $content = self::getSubContent($curPageID);
    }  

    $ret = array($content, $curPage, $curHierarchy, $curHierLen, $curHierDepth);
    return $ret;
  } 
  
  static function &getSubContent($startNodeId)
  {
    $gCms = cmsms();
    $content = array();
    $hm = $gCms->GetHierarchyManager();
    /* Works with new addition to Tree, but getFlatList is default
     $rn = $hm->sureGetNodeById($startNodeId); 
     $count = 0;
     $hm->getFlattenedChildren($rn, $content, $count);
    */
    $content = $hm->getFlatList();
    return $content;
  }

  static function &getAllContent()
  {
    $gCms = cmsms();
    $content = array();
    $hm = $gCms->GetHierarchyManager();
    $rn = $hm->GetRootNode(); 
    $count = 0;
    $hm->getFlattenedChildren($rn, $content, $count);
    return $content;
  }

  static function getCatalogItemsList(&$params)
  {
    $lastcat = "";
    $lastcatfull = null;
    
    list($content, $curPage, $curHierarchy, $curHierLen, $curHierDepth) = self::GetContentFromParams($params);
    
    $categoryItems = array();

    foreach ($content as $thisPage)
    {
      $thispagecontent = $thisPage->GetContent();

      if( empty($thispagecontent) ) continue; 
      if( !self::_IsValid($thispagecontent) ) continue; 
      if( !$thispagecontent->Id() == $curPage->Id() ) continue;
            
      if ($thispagecontent->Type() == 'contentalias')
      {
         $thispagecontent = $thispagecontent->GetAliasContent();
         $curHierLen = strlen($curHierarchy);
         $curHierDepth = substr_count($curHierarchy, '.');
      }
      
      if ($thispagecontent->Type() == 'catalogcategory')
      {
        $lastcat = $thispagecontent->Name();
      }
    
      if ( !self::_IsValidType($thispagecontent, $params) )
      {
        continue;
      }

      if ( ! self::_IsValidDepth($thispagecontent, $params, $curHierDepth, $curHierLen, $curHierarchy) )
      {
          continue;
      }
     
      // in the category, and approved for addition
      $thisItem = self::itemToArray($thispagecontent, $lastcat);
      array_push($categoryItems, $thisItem);
    }

    return array($curPage, $categoryItems);
  }

  static function getCatalogItemsIDList(&$params)
  {
    $gCms = cmsms();
    $db = $gCms->GetDb();
    $ret = array();
    
    if (!isset($params['alias']) || $params['alias']=='/')
    {
      $dbresult = $db->Execute('SELECT content_id from ' . cms_db_prefix() . 
           'content where type=\'catalogitem\'');
    }
    else
    {
      $base_hierarchy = $db->GetOne('SELECT hierarchy from ' . cms_db_prefix() . 
           'content where content_alias=?',array($params['alias']));
      if (! $base_hierarchy)
      {
        return $ret;
      }
      $dbresult = $db->Execute('SELECT content_id from '.cms_db_prefix().
           'content where type=\'catalogitem\' and hierarchy LIKE \'' . $base_hierarchy . '%\'');
    }
    
    while ($dbresult !== false && $row = $dbresult->FetchRow())
    {
      array_push($ret,$row['content_id']);
    }
        
    return $ret;
  }
  
  static function &getCatalogItem($alias)
  {
    $gCms = cmsms();
    $mod = cms_utils::get_module('Cataloger');
    $hm = $gCms->GetHierarchyManager();
    $pageNode = $hm->sureGetNodeByAlias($alias);
    $page = $pageNode->GetContent();
    $node = self::itemToArray($page, '');
    return $node;
  }

  static function &getCatalogItemById($id)
  {
    $gCms = cmsms();
    $mod = cms_utils::get_module('Cataloger');
    $hm = $gCms->GetHierarchyManager();
    $pageNode = $hm->sureGetNodeById($id);
    $page = $pageNode->GetContent();
    $node = self::itemToArray($page, '');
    return $node;
  }


  static function itemToArray($pagecontent, $lastcat)
  {
    $mod = self::_getMod();
    $thisItem = array();
    $catThumbSize = $mod->GetPreference('category_image_size_thumbnail', 90);
    $itemThumbSize = $mod->GetPreference('item_image_size_category', 70);
    $missingImage = $mod->GetPreference('show_missing', '1');
    
    switch ($pagecontent->Type())
    {
      
      case 'catalogitem':
      {
        $thisItem['image'] = catFilesOperations::imageSpec(
                                                            $pagecontent->Alias(),
                                                            's', 
                                                            1, 
                                                            $itemThumbSize
                                                          );
                                              
        $thisItem['image_src'] = catFilesOperations::srcImageSpec(
                                                                    $pagecontent->Alias(),
                                                                    1
                                                                  );
      }
      break;
                          
      case 'catalogcategory':
      {
        $thisItem['image'] = catFilesOperations::imageSpec(
                                                              $pagecontent->Alias(),
                                                              'ct', 
                                                              1, 
                                                              $catThumbSize
                                                           );
      }
      break;
    }
      
    $thisItem['link'] = $pagecontent->GetUrl();
    $thisItem['title'] = $pagecontent->Name();
    $thisItem['alias'] = $pagecontent->Alias();
    $thisItem['menutitle'] = $pagecontent->MenuText();
    $thisItem['modifieddate'] = $pagecontent->GetModifiedDate();
    $thisItem['category'] = $lastcat;
    $thisItem['cat'] = $lastcat;
    $thisItem['createdate'] = $pagecontent->GetCreationDate();
    $thisItem['attrs'] = array();
    $theseAttrs = $pagecontent->getAttrs();

    foreach ($theseAttrs as $thisAttr)
    {
      $safeattr = strtolower(preg_replace('/\W/', '', $thisAttr->attr));
      $thisItem[$thisAttr->safe] = $pagecontent->GetPropertyValue($thisAttr->attr);
      $thisItem['attrs'][$thisAttr->safe] = $pagecontent->GetPropertyValue($thisAttr->attr);
      
      if( !isset($thisAttr->alias) || empty($thisAttr->alias) )
      {
        $thisAttr->alias = strtolower( preg_replace('/\W/', '', $thisAttr->attr) );
      }
      
      # prolly redundant 'if' and to be revoved if so... (JM)
      if( isset($thisAttr->alias) && !empty($thisAttr->alias) )
      {
        $thisItem[$thisAttr->alias] = $pagecontent->GetPropertyValue($thisAttr->attr);
        $thisItem['attrs'][$thisAttr->alias] = $pagecontent->GetPropertyValue($thisAttr->attr);  
      }
    }
    
    return $thisItem;
  }
  
  static protected function _IsActive(&$content)
  {
    return ( method_exists($content, 'Active') && $content->Active() );
  }  
    
  static protected function _IsValid(&$content)
  {
    return ( is_object($content) || ( !self::_IsActive($content) ) );
  }
  
  static protected function _IsValidType(&$content, &$params)
  {
    $type_ok = FALSE;
    $ct = $content->Type();
    
    switch ($ct)
    {
      case 'catalogitem':
      {
        $RecursiveModes = array('items_one', 'items_all', 'mixed_one', 'mixed_all');

        if ( in_array( $params['recurse'], $RecursiveModes) )
        {
          $type_ok = TRUE; 
        } 
      }
      break;
      
      case 'catalogcategory':
      {
        $RecursiveModes = array('categories_one', 'categories_all', 'mixed_one', 'mixed_all');

        if ( in_array( $params['recurse'], $RecursiveModes) )
        {
          $type_ok = TRUE; 
        } 
        
      }
      break;
    }
  
    return $type_ok;
  }
  
  
  static protected function _IsValidDepth(&$content, &$params, $curHierDepth, $curHierLen, $curHierarchy)
  {
    $depth_ok = false;

    $RecursiveModesOne = array('items_one', 'categories_one', 'mixed_one');
    $RecursiveModesAll = array('items_all', 'categories_all', 'mixed_all');
    $dot = substr( $content->Hierarchy(), 0, $curHierLen + 1 ) == $curHierarchy . '.';
    
    
    if (  
          in_array( $params['recurse'], $RecursiveModesOne)  &&
          substr_count( $content->Hierarchy(), '.' ) == ( $curHierDepth + 1 ) &&
          $dot 
       )
    {
      $depth_ok = true;
    }
    elseif ( 
              ( isset($params['alias']) && $params['alias'] == '/') ||
              ( in_array( $params['recurse'], $RecursiveModesAll)  && $dot )
           )
    {
      $depth_ok = true;
    }
    
    return $depth_ok;
  } 
  
  ######################################################
  # Sorting
  ######################################################
  
  static function contentalpha($a, $b)
  {
    return strcasecmp($a['title'], $b['title']);
  }
  

  static function chrono($a, $b)
  {
    #return self::CompareDates($a['modifieddate'], $b['modifieddate']);
    # reverse to keep compatibility with previous
    return self::CompareDates($b['modifieddate'], $a['modifieddate']);
    /*
    if ($a['modifieddate'] > $b['modifieddate'])
    {
      return -1;
    }
    if ($a['modifieddate'] < $b['modifieddate'])
    {
      return 1;
    }
    return 0;
    */
  }

  static function created($a, $b)
  {
    #return self::CompareDates($a['createdate'], $b['createdate']);
    # reverse to keep compatibility with previous
    return self::CompareDates($b['createdate'], $a['createdate']);
    
    /*
    if ($a['createdate'] > $b['createdate'])
    {
      return -1;
    }
    if ($a['createdate'] < $b['createdate'])
    {
      return 1;
    }
    return 0;
    */
  } 
  ###################################################### 
  
  ######################################################
  # datetime
  ######################################################
  
  static function makeTimeStamp($hour = '', $minute = '', $second = '', $year = '', $month = '', $day = '')
  {
     if(empty($hour)) 
     {
         $hour = strftime('%H');
     }
     if(empty($minute)) 
     {
         $minute = strftime('%M');
     }
     if(empty($second)) 
     {
         $second = strftime('%S');
     }
     if(empty($year)) 
     {
         $year = strftime('%Y');
     }
     if(empty($month)) 
     {
         $month = strftime('%m');
     }
     if(empty($day)) 
     {
         $day = strftime('%d');
     }

     return mktime($hour, $minute, $second, $month, $day, $year);
  }

  static function Now()
  {
    return self::_getDB()->DbTimeStamp(time());
  }
  
  #
  # Redirect
  #
  
  /**
  * Shows a very close approximation of an Apache generated 301 error.
  * It also sends the actual header along as well, so that generic
  * browser error pages (like what IE does) will be displayed.
  */
  static function ErrorHandler301($url)
  {
    // TODO 0 -o JoMorg -c  task: Clean $url
    // TODO 0 -o JoMorg -c  task: Localize... maybe?
    @ob_end_clean();
    #header($_SERVER["SERVER_PROTOCOL"] . ' 301 Moved Permanently'); # maybe????
    header('HTTP/1.1 301 Moved Permanently');
    header("Status: 301  Moved Permanently");
    # make sure browsers don't cache 
    header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
    header('Location: ' . $url );
    $html = "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">\n"; 
    $html .= "<html>\n"; 
    $html .= "<head>\n"; 
    $html .= "<title>Moved</title>\n"; 
    $html .= "</head>\n"; 
    $html .= "<body>\n"; 
    $html .= "<h1>Moved</h1>\n"; 
    $html .= "<p>This page has moved to <a href=\"" . $url . "\">" . $url . "</a>.</p>\n"; 
    $html .= "</body>\n"; 
    $html .= "</html>\n"; 
    echo $html;
    exit();
  }
  
  /**
   * Given a page ID or an alias, redirect to it
   * Retrieves the URL of the specified page, and performs a redirect
   *
   * @param mixed An integer page id or a string page alias.
   * @return void
   */
  static function URLfromAlias($alias)
  {
    $manager = cmsms()->GetHierarchyManager();
    $node = $manager->sureGetNodeByAlias($alias);
    
    if( !$node ) 
    {
      audit('', 'Cataloger', 'Attempt to redirect to invalid alias: ' . $alias);
      return;
    }
    
    $content = $node->GetContent();
    
    if (!is_object($content)) 
    {
      audit('', 'Cataloger', 'Attempt to redirect to invalid alias: ' . $alias);
      return;
    }
    
    return $content->GetURL();
  }
  
  static function IsValidAlias($alias)
  {
    $manager = cmsms()->GetHierarchyManager();
    $node = $manager->sureGetNodeByAlias($alias);

    if( !$node ) 
    {
      return false;
    }
    
    $content = $node->GetContent();
    
    if (!is_object($content)) 
    {
      return false;
    }
    
    return true; 
  }

  static function  is_Expired($date)
  {
    return (bool)( 1 === self::CompareDates( time(), $date ) );
  }
  
  static function  to_Expire($date)
  {
    return (bool)( 1 === self::CompareDates( time(), $date ) );
  }
  
  static function to_UnExpire($date)
  {
    return (bool)( 1 === self::CompareDates( $date, time() ) ); 
  }
  
  static function CompareDates($a, $b)
  {
    # make sure they're integers....
    $a = (int)$a;
    $b = (int)$b;
    $r = 0;
      
    if ($a > $b)
    {
      $r = 1;
    }
    
    if ($a < $b)
    {
      $r = -1;
    }
    
    return (int)$r;
  }
  
}
?>