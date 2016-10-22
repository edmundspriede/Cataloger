<?php
class catUserDefAttributesOps
{
  private static $attrs = array();
  private static $_db;
  
  private static function _getDB()
  {
    
    if ( !is_object(self::$_db) )
    {
      self::$_db = cmsms()->GetDb();
    }
    
    return self::$_db;
    
  }
  
  static function &getUserAttributes($global_ref = 'catalog_attrs')
  {
      $db = self::_getDB();
      self::$attrs[$global_ref] = array();
      
      $query = "SELECT attribute, alias, defaultval, length, field_type, select_values, order_by FROM "
               . cms_db_prefix()
               . "module_catalog_attr WHERE type_id=? ORDER BY order_by ASC";
               
      $type_id = 1;
      
      if ($global_ref == 'catalog_cat_attrs')
      {
        $type_id = 2;
      }
      elseif ($global_ref == 'catalog_print_attrs')
      {
        $type_id = 3;
      }
      
      $dbresult = $db->Execute($query, array($type_id));
      
      while ($dbresult !== false && $row = $dbresult->FetchRow())
      {
        $Attr = new stdClass();
        $Attr->attr = $row['attribute'];
        $Attr->alias = !empty($row['alias']) ? $row['alias'] : strtolower( preg_replace('/\W/', '', $row['attribute']) );
        $Attr->length = $row['length'];
        $Attr->default = $row['defaultval'];
        $Attr->select_values = $row['select_values'];
        $Attr->field_type = $row['field_type'];
        $Attr->field_order = $row['order_by'];
        $Attr->safe = strtolower( preg_replace('/\W/', '', $row['attribute']) );
        self::$attrs[$global_ref][$Attr->alias] = $Attr;
      }

    return self::$attrs[$global_ref];
  }  
}
?>
