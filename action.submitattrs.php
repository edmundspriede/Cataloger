<?php
if( !defined('CMS_VERSION') ) exit;
if( !$this->CheckAccess() ) exit;

$contentops = cmsms()->GetContentOperations();

$types = array(
                 '',
                 'catalogitem',
                 'catalogcategory',
                 'catalogprintable'
              );

foreach($params as $thisParamKey => $thisParamValue)
{
  if( substr($thisParamKey, 0, 4) == 'attr') # is attr_*_*_*   (JM)
  {
    # at this point $thisParamKey should hold something like 'attr_attrType_attrNum' whatever they may represent... (JM)
    list($attr, $attrType, $attrNum) = explode('_', $thisParamKey);
    $thisalias = $params['alias_' . $attrType . '_' . $attrNum];

    if( 
        (strlen($thisParamValue) > 0) 
        && (!isset($params['delete_' . $attrType . '_' . $attrNum])
        || $params['delete_' . $attrType . '_' . $attrNum] == '0') )
    {
      // new attribute or updating old?
      if( isset($params['old_' . $attrType . '_' . $attrNum]) )
      {
        // it's an old record

        // get old value to update content table
        $query = 'SELECT attribute,alias FROM ' . cms_db_prefix() . 'module_catalog_attr where id=?';
        $r = $db->GetArray( $query, array ($params['old_' . $attrType . '_' . $attrNum]) );  
        //$r = $db->GetOne($query, array ($params['old_' . $attrType . '_' . $attrNum]));  
//                   
//        if($r)
//        {
//          $oldval = $r['attribute'];
//          $oldalias = $r['alias'];
//        }
        if($r)
        {
          $oldval = isset($r['attribute']) ? $r['attribute'] : '';
          $oldval = isset($r['alias']) ? $r['alias'] : '';
        }
        //$oldval = $db->GetOne($query, array ($params['old_' . $attrType . '_' . $attrNum]));


        // write new value
        $query =  'UPDATE ' 
                  . cms_db_prefix()
                  . 'module_catalog_attr SET attribute=?, alias=?, order_by=?, defaultval=?, length=?, select_values=?, field_type=? where id=?';
                  
        $dbresult = $db->Execute($query, array
        (
           $thisParamValue,
           $params['alias_'           . $attrType . '_' . $attrNum],
           $params['orderby_'         . $attrType . '_' . $attrNum],
           $params['default_'         . $attrType . '_' . $attrNum],
           $params['len_'             . $attrType . '_' . $attrNum],
           $params['select_values_'   . $attrType . '_' . $attrNum],
           $params['field_type_'      . $attrType . '_' . $attrNum],
           $params['old_'             . $attrType . '_' . $attrNum]
        ));

        if ( $dbresult === false )
        {
          return $this->displayError( $db->ErrorMsg() );
        }

        // update content table
        if($oldval != $thisParamValue)
        {
          $query =  'SELECT c.content_id from ' 
                    . cms_db_prefix() 
                    . 'content c, ' 
                    . cms_db_prefix()
                    . 'content_props p where c.type=? and p.prop_name=? and c.content_id=p.content_id';
          
          $dbresult = $db->Execute(
                                    $query, 
                                    array(
                                           $types[$attrType],
                                           $oldval
                                          )
                                  );
                                                
          $clist = array ();

          while( $dbresult !== false && $row = $dbresult->FetchRow() )
          {
            $clist[] = $row['content_id'];
          }

          if ( count($clist) > 0 ) # if our list isn't empty
          {
            foreach( $clist as $one )
            {
              $obj = $contentops->LoadContentFromId($one, TRUE);
              $obj->UpdatePropertyName($oldalias, $thisalias);
              $obj->save();
            }
          }
        }
      }
      else # it's a new attribute
      {
        $new_id = $db->GenID(cms_db_prefix() . 'module_catalog_attr_seq');
        
        $query =  'INSERT INTO ' 
                  . cms_db_prefix()
                  . 'module_catalog_attr (id,type_id,attribute,alias,defaultval,length,order_by,select_values,field_type) VALUES (?,?,?,?,?,?,?,?,?)';
        
        $dbresult = $db->Execute($query, array
        (
           $new_id,
           $attrType,
           $thisParamValue,
           $params['alias_' . $attrType . '_' . $attrNum],
           $params['default_' . $attrType . '_' . $attrNum],
           $params['len_' . $attrType . '_' . $attrNum],
           $params['orderby_' . $attrType . '_' . $attrNum],
           $params['select_values_' . $attrType . '_' . $attrNum],
           $params['field_type_' . $attrType . '_' . $attrNum]
        ));

        if ( $dbresult === false )
        {
          return $this->displayError($db->ErrorMsg());
        }

        // update content table for existent records!
        if( !empty($params['default_' . $attrType . '_' . $attrNum]) )
        {
          $query = 'SELECT content_id' . cms_db_prefix() . 'content where type=?';
          $dbresult = $db->Execute($query, array ($types[$attrType]));
          $clist = array ();

          while( $dbresult !== false && $row = $dbresult->FetchRow() )
          {
            $clist[] = $row['content_id'];
          }

          if ( count($clist) > 0 )
          {
            foreach( $clist as $one )
            {
              $obj = $contentops->LoadContentFromId($one, TRUE);
              $obj->AddProperty($thisalias);
              $obj->save();
            }
          }
        }
      }
    }
    elseif( isset($params['delete_' . $attrType . '_' . $attrNum])
        && $params['delete_' . $attrType . '_' . $attrNum] == '1' )
    // this is a delete operation (JM)
    {
      // delete from Content Table as well!
      $query = 'SELECT attribute FROM ' . cms_db_prefix() . 'module_catalog_attr where id=?';
      $did = $db->GetOne($query, array ($params['old_' . $attrType . '_' . $attrNum]));

      $query = 'SELECT content_id from ' . cms_db_prefix() . 'content where type=?';
      $dbresult = $db->Execute($query, array ($types[$attrType]));
      $clist = array ();

      while( $dbresult !== false && $row = $dbresult->FetchRow() )
      {
        $clist[] = $row['content_id'];
      }

      if ( count($clist) > 0 )
      {
        foreach( $clist as $one )
        {
          $obj = $contentops->LoadContentFromId($one, TRUE);
          $obj->DeleteProperty($thisParamValue);
          $obj->save();
        }
      }

      // handle deletes explicitly now.
      $query = 'DELETE FROM ' . cms_db_prefix() . 'module_catalog_attr where id=?';
      $dbresult = $db->Execute($query, array ($params['old_' . $attrType . '_' . $attrNum]));
    }
  }
}

$gCms = cmsms();
//$gCms->clear_cached_files();   --> apparently an illegal op too.... (JM) Unless it only clears own cached files...
$params['module_message'] = $this->Lang('attrsupdated');
$this->DoAction('adminattrs', $id, $params);
?>