<?php
		if( !defined('CMS_VERSION') ) exit;
		if (!isset($params['name']))
			{
			echo '<!-- please specify a Cataloger variable name to retrieve -->';
			return;
			}

			$found = false;
			$gCms = cmsms();
			$contentops = $gCms->GetContentOperations();
			$content_obj = $contentops->getContentObject();			
		if ($content_obj)
			{
			if ($content_obj->Type() != 'catalogitem' && $content_obj->Type() != 'catalogcategory' && $content_obj->Type() != 'catalogprintable')
				{
				return;
				}
			$attrs = $content_obj->getAttrs();
			$testattr = strtolower($params['name']);
			
			foreach ($attrs as $thisAttr)
				{
				$compattr = strtolower($thisAttr->attr);
				if ($compattr == $testattr)
					{
					$tmp = $content_obj->GetPropertyValue($thisAttr->attr);
					$found = true;
					}
				else if ($thisAttr->alias == $testattr)
					{
					$tmp = $content_obj->GetPropertyValue($thisAttr->alias);
					$found = true;
					}
				else
					{
					$safeattr = strtolower(preg_replace('/\W/','', $params['name']));
					if ($compattr ==  $safeattr || $thisAttr->alias == $safeattr)
						{
						$tmp = $content_obj->GetPropertyValue($safeattr);
						$found = true;
						}
					}
				}
			}

		if (! $found)
			{
			if (isset($params['default']))
				{
				$tmp = $params['default'];
				}
			else
				{
				$tmp = '';
				}
			}
		echo $tmp;
?>