<?php
if( !defined('CMS_VERSION') ) exit;
if (! $this->CheckAccess()) exit;

        /**
        * This IS one of the actions to change (JM)
        */
        
        $contentops = cmsms()->GetContentOperations();
		if (isset($params['item_sort_order']) &&
			$params['item_sort_order'] != 'nochange')
			{
			// update all item sort order

            // new legal code. (JM)
              $query = "SELECT c.content_id from " . 
                        cms_db_prefix() . "content c, " .
                        cms_db_prefix() . "content_props p " .
                        "where c.type='catalogcategory' and cp.prop_name='sort_order' and c.content_id=p.content_id";

                $tmp = $db->GetCol($query);
                
               
                foreach( $tmp as $one ) 
                {  
                    $obj = $contentops->LoadContentFromId($one,TRUE);
                    $obj->SetPropertyValue('sort_order',$params['item_sort_order']);
                    $obj->save(); 
                }
			}
		if (isset($params['category_recurse']) &&
			$params['category_recurse'] != 'nochange')
			{
			// update display rules
            
             // new legal code. (JM)
             
              $query = "SELECT c.content_id from " . 
                        cms_db_prefix() . "content c, " .
                        cms_db_prefix() . "content_props p " .
                        "where c.type='catalogcategory' and p.prop_name='recurse' and c.content_id=p.content_id";

                $tmp = $db->GetCol($query);
                foreach( $tmp as $one ) 
                {  
                    $obj = $contentops->LoadContentFromId($one,TRUE);
                    $obj->SetPropertyValue('sort_order',$params['category_recurse']);
                    $obj->save(); 
                }
			}
		if (isset($params['items_per_page']) &&
			$params['items_per_page'] != -1)
			{
			// update display rules
            

            $query = "SELECT c.content_id from " . 
                    cms_db_prefix() . "content c, " .
                    cms_db_prefix() . "content_props p " .
                    "where c.type='catalogcategory' and cp.prop_name='items_per_page' and c.content_id=p.content_id";           

                $tmp = $db->GetCol($query);
                foreach( $tmp as $one ) 
                {  
                    $obj = $contentops->LoadContentFromId($one,TRUE);
                    $obj->SetPropertyValue('sort_order',$params['items_per_page']);
                    $obj->save(); 
                }                            
			}


		$params['message'] = $this->Lang('globallyupdated');
        $this->DoAction('globalops', $id, $params);

?>
