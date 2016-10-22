<?php
class CatContentBaseType extends Content
{
  # internal
  protected $_mod;
  protected $validation = FALSE;
  
  # we need these to set some defaults
  protected $catExpired;
  protected $catUseExpirationDate;
  protected $catExpirationStartDate;
  protected $catExpirationEndDate;
  
  # user attributes array
  protected $attrs;

  function __construct()
  {
    parent::__construct();
  }
    
  function &get_mod()
  {
    if( is_null($this->_mod) )
    {
      $this->_mod = cms_utils::get_module('Cataloger');
    }
    return $this->_mod;
  }
      
  function Lang($key)
  {
    return $this->get_mod()->Lang($key);
  }
        
  function get_lang($key)
  {
    return $this->get_mod()->Lang($key);
  }
  
  function GetAdminThemeName()
  {
    return cms_utils::get_theme_object()->GetDefaultTheme(); 
  }
  
  protected function SetDefaults()
  {
    $temp = $this->GetPropertyValue('expired');
    
    if( !isset($temp) )
    {
      $this->SetPropertyValue('expired', $this->catExpired);
    }
    
    $temp = $this->GetPropertyValue('use_expiration_date');
    
    if( !isset($temp) )
    {
      $this->SetPropertyValue('use_expiration_date', $this->catUseExpirationDate);
    }
    
    $temp = $this->GetPropertyValue('expiration_start_date');
    
    if( !isset($temp) )
    {
      $this->SetPropertyValue('expiration_start_date', $this->catExpirationStartDate);
    }
    
    $temp = $this->GetPropertyValue('expiration_end_date');
    
    if( !isset($temp) )
    {
      $this->SetPropertyValue('expiration_end_date', $this->catExpirationEndDate);
    }
    
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

  function GetCreationDate()
  {
    return $this->mCreationDate;
  }

  function GetModifiedDate()
  {
    return $this->mModifiedDate;
  }

  function HasAttributes()
  {
    return is_array($this->attrs) && (count($this->attrs) > 0);
  }
  
  function SetProperties()
  {
    parent::SetProperties();
    
    $this->getUserAttributes();
    
    #lets add the fields
    if (is_array($this->attrs))
    {
      foreach ($this->attrs as $thisAttr)
      {
        $this->AddContentProperty($thisAttr->attr, 100 + $thisAttr->field_order);
      }
    }

    # since 0.12 (JoMorg)
    $this->AddContentProperty('expired', 101);
    $this->AddContentProperty('use_expiration_date', 102);

    $this->AddContentProperty('expiration_start_date', 103);
    $this->AddContentProperty('expiration_end_date', 104);
         
    # let's set defaults   
    
    // turn on preview
    $this->mPreview = true;

    // turn off caching
    $this->mCachable = false;
    
    // not expired by default
    $this->catExpired = false;
    
    // don't use expiration date
    $this->actUseExpirationDate = false;
    
    $now = time();
    
    // defaults to now
    $this->actExpirationStartDate = $now;

    $ndays = (int)$this->get_mod()->GetPreference('expiry_interval', 180);
    if( $ndays == 0 )
    {
      $nyears = 1;
      $end_date = strtotime( sprintf( "+%d years", $nyears ), time() ); 
    }
    else
    {
      $end_date = strtotime( sprintf( "+%d days", $ndays ), time() );
    }
    
    $this->actExpirationEndDate = $end_date;

  }
  
  function getUserAttributes()
  {
    # just so we don't get an error
  }
  
  function getAttrs()
  {
    $this->getUserAttributes();
    return $this->attrs;
  }

  function FillParams($params, $editing = false)
  {
    
    if( !isset($params) ) return;

    $parameters = array('sub_template', 'use_expiration_date');

    foreach($parameters as $oneparam)
    { 
      if( isset($params[$oneparam]) )
      {
        $this->SetPropertyValue($oneparam, $params[$oneparam]);
      }
    }
      
    # special cases
    if( isset($params['StartDate_Month']) )
    {
      
      $startdate = catContentOperations::makeTimeStamp(
                                                        $params['StartTime_Hour'], 
                                                        $params['StartTime_Minute'], 
                                                        $params['StartTime_Second'],
                                                        $params['StartTime_Year'], 
                                                        $params['StartDate_Month'], 
                                                        $params['StartDate_Day']
                                                      );
                                          
      $this->SetPropertyValue('expiration_start_date', $startdate);
    }      
    
    if( isset($params['EndDate_Month']) )
    {
      
      $enddate = catContentOperations::makeTimeStamp(
                                                        $params['EndTime_Hour'], 
                                                        $params['EndTime_Minute'], 
                                                        $params['EndTime_Second'],
                                                        $params['EndDate_Year'], 
                                                        $params['EndDate_Month'], 
                                                        $params['EndDate_Day']
                                                      );
                                                      
      $this->SetPropertyValue('expiration_end_date', $enddate);
    }
    
    if( isset($params['expired']) )
    {
      $mod = $this->get_mod();
      $ExpiryAction = $mod->GetPreference('OnExpiryAction', 3);
      
      if ($this->catExpired <> $params['expired'])
      {
        $this->catExpired = $params['expired'];
        if ($params['expired'])
        {
          $this->_doExpire($ExpiryAction, true, false); 
        }
        else
        {
          $this->_doUnExpire(true, false); 
        }

      }
    }
    
    parent::FillParams($params);
  }

  
  function ValidateData()
  {
    $errors = parent::ValidateData();
    
    if( $errors === FALSE )
    {
      $errors = array();
    }

    return ( count($errors) > 0 ? $errors : FALSE );
  }

  /**
  * Add extra tabs
  */
 
  protected function _TabLogic()
  {
    $hash = array('main', 'options', 'cataloger_options');
    
    if ( $this->HasAttributes() )
    {
      array_push($hash, 'attributes');
    }

    return $hash;
  }
 
  public function TabNames()
  {
    $ret = parent::TabNames();
    $mod = $this->get_mod();
    array_push( $ret, $mod->lang('title_cataloger_extras') );
    
    if ( $this->HasAttributes() )
    {
      array_push( $ret, $mod->lang('title_cataloger_attrs') );
    }

    return $ret;
  }
  
  function EditAsArray($adding = false, $tab = 0, $showadmin = false)
  {
    $mod = $this->get_mod();
    $ret = parent::EditAsArray($adding, $tab, $showadmin);
    
    $tabHash = $this->_TabLogic();
    
    if ($tabHash[$tab] == 'attributes')
    { 
      $this->getUserAttributes();

      foreach ($this->attrs as $thisAttr)
      {
        $ret[] = catFields::RenderInput($thisAttr, $this->GetPropertyValue($thisAttr->attr));
      }
    }
    
    if ($tabHash[$tab] == 'cataloger_options')
    {
      ## some Warnings here #####################################################
      if ($this->mAlias == '')
      {
        $warningimg = '<img alt="'
                      . $mod->Lang('no_alias_warning')
                      . '" src="'
                      . $this->GetWarningImage()
                      . '" />';
        $warning = $mod->Lang('no_alias_warning');
        
        $ret[] = array ($warningimg, $warning);
        $ret[]= array('<hr />');
      }
            
      if (!$this->HasAttributes())
      {
        $warningimg = '<img alt="'
                      . $mod->Lang('no_attrs_warning')
                      . '" src="'
                      . $this->GetWarningImage()
                      . '" />';
        $warning = $mod->Lang('no_attrs_warning');
        
        $ret[] = array ($warningimg, $warning);
        $ret[]= array('<hr />');
      }
      ##                    #####################################################

      $ret[]= $this->display_single_element_ext('use_expiration_date');
      $ret[]= $this->display_single_element_ext('expired');
//      $ret[]= array('<hr />');
//      $ret[]= $this->display_single_element_ext('expiration_start_date');
//      $ret[]= $this->display_single_element_ext('expiration_start_time'); #special case
      $ret[]= array('<hr />');
      $ret[]= $this->display_single_element_ext('expiration_end_date');
      $ret[]= $this->display_single_element_ext('expiration_end_time');   #special case
    }

    return $ret;
  }
   
  /**
  * We override this to make shure all properties
  * are known to CMSMS, and call parent method
  * to enforce the default behaviour.
  * But we deal with the actual displaying
  * of the elements on our own extended version
  * of this method. ( display_single_element_ext() )
  *  
  * 
  * @param mixed $one
  * @param mixed $adding
  * @return void
  */
  protected function display_single_element($one, $adding)
  {
    # we just want to make sure the new ones are known to CMSMS
    $this->getUserAttributes();
    $elements = array();
    
    foreach ($this->attrs as $element)
    {
      array_push($elements, $element->attr);
    }
    
    if (in_array($one, $elements))
    {
      $r = array();
      return $r;
    }
    
    $elements = array(
                        'use_expiration_date', 
                        'expired',
                        'expiration_start_date',  
                        'expiration_end_date'
                      );
    
    if (in_array($one, $elements))
    {
      $r = array();
      return $r;
    }
    
    return parent::display_single_element($one, $adding);

  }
  
  /**
  * Our own version of display_single_element()
  * prevents displaying properties out of the pertinent tabs
  * and allows for some expecial treatment of date and time fields  
  * 
  * @param mixed $one
  * @param mixed $adding
  */
  protected function display_single_element_ext($one, $adding = false)
  {
    $gCms = cmsms();
    $mod = $this->get_mod();
    $smarty = $gCms->GetSmarty();
    $this->SetDefaults();

    switch($one)
    {
      
      case 'use_expiration_date':
      {
        $warningimg = '<img alt="'
                      . $mod->Lang('no_attrs_warning')
                      . '" src="'
                      . $this->GetWarningImage()
                      . '" />';
                      
        $warning = $warningimg . ' ' . $mod->Lang('warning_prompt_use_expiration');
        
        $global = $mod->GetPreference('UseExpDate', FALSE);
        $value = $this->GetPropertyValue('use_expiration_date');
        $r = array (
                      '<label for="in_use_expiration_date">'
                      . $mod->Lang('prompt_use_expiration')
                      . ':</label>',
                      '<input type="hidden" name="use_expiration_date" value="0"/>
                      <input id="in_use_expiration_date" class="pagecheckbox" type="checkbox" value="1" name="use_expiration_date"'
                      . ($value ? ' checked = "checked"' :'' )
                      . ' />',
                      $mod->Lang('help_prompt_use_expiration')
                      . ($global ? '' :( '<br /><em>' . $warning . '</em>'  ) )
                    );
        return $r;
      }
      break;
      
//      case 'expiration_start_date':   #still needs more work
//      {
//        $value = $this->GetPropertyValue('expiration_start_date');
//        $field = '{html_select_date  prefix=\'StartDate_\' field_order=\'DMY\' start_year=\'-1\' end_year=\'+100\' time=' . $value .'}';
//        $r = array ($mod->Lang('prompt_expiration_start_date') . ':', $smarty->fetch('eval:'.$field));
//        return $r;        
//      }
//      break;
//      
//      case 'expiration_start_time':
//      {
//        $value = $this->GetPropertyValue('expiration_start_date');
//        $field = '{html_select_time  prefix=\'StartTime_\' time=' . $value .'}';
//        $r = array ($mod->Lang('prompt_expiration_start_time') . ':', $smarty->fetch('eval:'.$field));
//        return $r;        
//      }
//      break;
          
      case 'expiration_end_date':
      {
        $value = $this->GetPropertyValue('expiration_end_date');
        $field = '{html_select_date  prefix=\'EndDate_\' field_order=\'DMY\' start_year=\'-1\' end_year=\'+100\' time=' . $value .'}';
        $r = array ($mod->Lang('prompt_expiration_end_date') . ':', $smarty->fetch('eval:'.$field));
        return $r;    
      }
      break;
    
      case 'expiration_end_time':
      {
        $value = $this->GetPropertyValue('expiration_end_date');
        $field = '{html_select_time  prefix=\'EndTime_\' time=' . $value .'}';
        $r = array ($mod->Lang('prompt_expiration_end_time') . ':', $smarty->fetch('eval:'.$field));
        return $r;     
      }
      break;
       
      case 'expired':
      {
        $warningimg = '<img alt="'
                      . $mod->Lang('no_attrs_warning')
                      . '" src="'
                      . $this->GetWarningImage()
                      . '" />';
                      
        $warning = $warningimg . ' ' . $mod->Lang('warning_prompt_expired');
        
        $global = $mod->GetPreference('UseExpDate', FALSE);
        $value = $this->GetPropertyValue('expired');
        $r = array (
                      '<label for="in_expired">'
                      . $mod->Lang('prompt_expired')
                      . ':</label>',
                      '<input type="hidden" name="expired" value="0"/>
                      <input id="in_expired" class="pagecheckbox" type="checkbox" value="1" name="expired"'
                      . ($value ? ' checked = "checked"' :'' )
                      . ' />',
                      $mod->Lang('help_prompt_expired')
                      . ($global ? '' :( '<br /><em>' . $warning . '</em>' ) )
                    );
        return $r;
      }
    
    }
    
  }

  function Show($param='')
  {
    $txt = '';
    $cnts = parent::Show($param);

    if( $param != 'content_en' && $param != 'content' ) 
    {
      // must be displaying some other block.
      return $cnts;
    }
    
    $mod = $this->get_mod();
    $global = $mod->GetPreference('UseExpDate', FALSE);
    $local = $this->GetPropertyValue('use_expiration_date');
    
    if ( ($global && $local) ) # so we have to process expiration dates
    {
      $txt = $this->ProcessExpiration();
    }
    else # we don't have to process expiration dates
    {
      $txt .= $this->_doShow();
    }
    
    return $txt;
  }
  
  function _doShow()
  {
    $params = array();
    $this->PopulateParams($params);
    $params['_default_cnt'] = $cnts;
    return $this->RenderContent($params);
  }

  /**
  * #
  * 
  * @param mixed $params
  */
  function RenderContent($params)
  {
    return '';
  }
  
  function ProduceExpiredContent($ExpiryAction)
  {
    $mod = $this->get_mod();   
    
    switch ($ExpiryAction) 
    {
       case 0:
       {
         # If we get here something gone wrong... we just send to _doExpire again...
         $this->_doExpire($ExpiryAction, false, true);
         # and hope it is for good this time... 
       }
       break;

       case 1:
       {
         # here we assume alias exists and is valid
         $redirect = $mod->GetPreference('expire_redirect_alias');
         $url = catContentOperations::URLfromAlias($redirect);
         catContentOperations::ErrorHandler301($url);
       }
       break;
       
       case 2:
       {
         # here we assume alias exists and is valid
         $redirect = $mod->GetPreference('expire_redirect_alias');
         $mod->redirectContent($redirect);
       }
       break;

       case 3:
       {
         $text = $mod->GetPreference('ExpirationText', $this->Lang('default_exp_text') );   
       }
       break;
    }

      # we only arrive here if no redirection or 301 status occur
      return $text; 
  }
  
  function ProcessExpiration()
  {
    $mod = $this->get_mod();
    $ExpiryAction = $mod->GetPreference('OnExpiryAction', 3);
    $expired = $this->GetPropertyValue('expired');
    $ExpirationStartDate = $this->GetPropertyValue('expiration_start_date');
    $ExpirationEndDate = $this->GetPropertyValue('expiration_end_date');
    #$un_expire =  catContentOperations::to_UnExpire($ExpirationStartDate);
    $expire = catContentOperations::to_Expire($ExpirationEndDate);
    
//    echo '<------------------------------------------------------><br>';
//    echo 'EXP_ACT = ' . $ExpiryAction . '<br>';
//    echo 'Exp = ' . ($expired ? 'true' : 'false' ) . '<br>';
//    echo 'Exp st date = ' . $ExpirationStartDate . '<br>';
//    echo 'Exp end date = ' . $ExpirationEndDate . '<br>';
//    echo 'Exp diff date = ' . ($ExpirationEndDate - $ExpirationStartDate) . '<br>';
//    echo 'now = ' . time() . '<br>';
//    echo 'End diff = ' . ( $ExpirationEndDate - time() ) . '<br>';
//    echo 'Start diff = ' . ( $ExpirationStartDate - time() ) . '<br>';
//    echo 'UnExp = ' . ($un_expire ? 'true' : 'false' ) . '<br>';
//    echo 'To Exp = ' . ($expire ? 'true' : 'false' ) . '<br>';
//    echo '<------------------------------------------------------><br>';
    
    
//      
//    if ($ExpirationStartDate >= $ExpirationEndDate)
//    {
//      # this should never happen
//      echo 'Expiration Date Range ERROR!';
//      exit;
//    }

    
    if ($expired) # already expired: either is marked for un_expired or just keep expired 
    {
      /*  TODO
      if ($un_expire)
      {
        $this->_doUnExpire(false, true);  
      }
      else
      */
      {
        return $this->ProduceExpiredContent($ExpiryAction);
      }

    }
    else # not expired? ok, so test for expiration or keep it on
    {
      
      if ($expire)
      {
        $this->_doExpire($ExpiryAction, false, true);  
      }
      else
      {
        $txt .= $this->_doShow();
      } 
    }
    
    return $txt;
  }  
  
  
  # Misc
  
  protected function _doExpire($mode, $editing = false, $trigger = false)
  {

    $mod = $this->get_mod();
    /**
    * Actions :
    *  0 - Deactivate and throws a 404).
    *     (Note: this is permanent dead as far as automation goes.... 
    *                       object won't get show() called if not active)
    *  1 - redirect 301 to alias
    *  2 - Redirect to alias
    *  3 - Replace the content by a text
    */ 

    
    # needs optimizing....
    switch ($mode) 
    {
       case 0:
       {
         $this->mActive = false;
         $this->mShowInMenu = false;
         $this->catExpired = true;
         $this->Update();
         $mod->clear_cache($this->mId);
         if ($trigger){$this->_doTrigger();}
         $mod->Audit($this->mId, $this->mType, 'Expired');
         #by redirecting to itself we leave the handling of the 401 to the CMSMS Core settings
         if (!$editing){$mod->redirectContent($this->mAlias);}
       }
       break;
       
       case 1:
       {
         $this->mShowInMenu = false;
         $this->catExpired = true;
         $this->Update();
         $mod->clear_cache($this->mId);
         if ($trigger){$this->_doTrigger();}
         $mod->Audit($this->mId, $this->mType, 'Expired');
         #redirect?
         if (!$editing){$mod->redirectContent($this->mAlias);}
       }
       break;
       
       case 2:
       {
         $this->mShowInMenu = false;
         $this->catExpired = true;
         $this->Update();
         $mod->clear_cache($this->mId);
         if ($trigger){$this->_doTrigger();}
         $mod->Audit($this->mId, $this->mType, 'Expired');
         #redirect?
         if (!$editing){$mod->redirectContent($this->mAlias);}
       }
       break;
              
       case 3:
       {
         $this->mShowInMenu = true;
         $this->catExpired = true;
         $this->Update();
         $mod->clear_cache($this->mId);
         if ($trigger){$this->_doTrigger();}
         $mod->Audit($this->mId, $this->mType, 'Expired');
         #redirect?
         if (!$editing){$mod->redirectContent($this->mAlias);}
       }
       break;
    }
  }
  
  protected function _doTrigger()
  {
    $mod = $this->get_mod();
    $now = time();
    
    $event_params = array(
                            'content'     => $this,
                            'timestamp'   => $now
                          );
                          
    $mod->SendEvent('OnContentEndDate', $event_params); 
  }
  
  protected function _doUnExpire($editing = false, $trigger = false)
  {
   
   $mod = $this->get_mod();
   $this->mShowInMenu = true;
   $this->catExpired = false;
   $this->Update();
   $mod->clear_cache($this->mId);
    
    if ($trigger)
    { # we trigger an event
    
      $event_params = array(
                              'content'     => $this,
                              'timestamp'   => $now
                            );
                            
      $mod->SendEvent('OnContentStartDate', $event_params);
    }
    
    $mod->Audit($this->mId, $this->mType, 'Un-Expired');
    die('duh');
    #if (!$editing){$mod->redirectContent($this->mAlias);}
  }
  
  protected function GetWarningImage()
  {
    $config = cmsms()->GetConfig();
    return $config['root_url']
           . '/admin/themes/'
           . $this->GetAdminThemeName()
           . '/images/icons/system/warning.gif';
  }
  
  function Save()
  {
    $this->SetPropertyValue('expired', $this->catExpired);
    parent::Save();
  }
  
  protected function Update()
  {
    $this->SetPropertyValue('expired', $this->catExpired);
    parent::Update();
  }

  protected function Insert()
  {
    parent::Insert();
  }
  
  
}

?>