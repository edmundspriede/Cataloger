<?php
class catFields
{
  static private $_field_types;
  static private $_mod;
  
  function __Construct()
  {
    self::$_mod = cms_utils::get_module('Cataloger');
    self::$_field_types = array(
                                  'text'      =>  $this->_mod->Lang('text'),
                                  'select'    =>  $this->_mod->Lang('dropdown'),
                                  'textarea'  =>  $this->_mod->Lang('textarea'),
                                  'checkbox'  =>  $this->_mod->Lang('checkbox')
                                );
  }
  
  static function getFieldTypes()
  {
    return self::$_field_types;
  }
  
  
  static function RenderSelectField($field = null, $value = null)
  {
    $select_values = array();
    $ret = '';
    if (isset($field->select_values) && $field->select_values != '')
    {
      $select_values = array_map(
                                  'trim',
                                  explode(
                                            ',',
                                            htmlspecialchars(
                                                              $field->select_values, 
                                                              ENT_QUOTES
                                                             )
                                          )
                                  );
    }
      
    $ret .= '<select type="dropdown" name="' . $field->safe . '">';
      
    foreach ($select_values as $one_val)
    {
      $ret .= '<option value="' . $one_val . '"';
      
      if (htmlspecialchars($value, ENT_QUOTES) == $one_val)
      {
        $ret .= ' selected="selected"';
      }
      
      $ret .= '>' . $one_val . '</option>';
    }
    
    $ret .= '</select>';
    
    return array($field->attr . ':', $ret);
  }
  
  static function RenderTextArea($field = null, $value = null, $enableWYSIWYG = TRUE)
  {
    return array(
                  $field->attr . ':', 
                  create_textarea(
                                    $enableWYSIWYG, 
                                    $value, 
                                    $field->safe, 
                                    '', 
                                    $field->attr, 
                                    '', 
                                    '', 
                                    80, 
                                    10
                                  )
                 );
  }
   
  static function RenderCheckBox($field = null, $value = null)
  {
    if (isset($field->select_values) && strpos($field->select_values, ',') !== false)
    {
      list ($is, $isnt) = explode(',', $field->select_values);
    }
    else
    {
      $is = 'Yes';
      $isnt = 'No';
    }
    
    $ret = '<input type="hidden" name="'
              . $field->safe
              . '" value="'
              . htmlspecialchars($isnt, ENT_QUOTES)
              . '"/>';
              
    $ret .= '<input type="checkbox" id="'
               . $field->safe
               . '" name="'
               . $field->safe
               . '" value="'
               . htmlspecialchars($is, ENT_QUOTES)
               . '"';
               
    if ($value == $is)
    {
      $ret .= ' checked="checked"';
    }
    
    $ret .= '><label for="' . $field->safe . '">' . $is . '</label>';
    
    return array($field->attr . ':', $ret);
  }
  
  static function RenderText($field = null, $value = null)
  {
    $l = $field->length;

    if (empty($l))
    {
      $l = 25;
      $m = 1024;
    }
    else
    {
      $m = $l;
    }
    
    return array(
                  $field->attr . ':', 
                  '<input type="text" name="'
                  . $field->safe
                  . '" value="'
                  . htmlspecialchars($value, ENT_QUOTES)
                  . '" size="'
                  . $l
                  . '" maxlength="'
                  . $m
                  . '" />'
                 );
  }
  
  static function RenderInput($field = null, $value = null, $enableWYSIWYG = TRUE)
  {

    if (empty($value) && !empty($field->default))
    {
      $value = $field->default;  
    }
    
    if ($field->field_type == 'select')
    {
      return self::RenderSelectField($field, $value);
    }
    else if ($field->field_type == 'textarea')
    {
      return self::RenderTextArea($field, $value, $enableWYSIWYG);
    }
    
    else if ($field->field_type == 'checkbox')
    {
      return self::RenderCheckBox($field, $value);
    }
    else
    {
      return self::RenderText($field, $value);
    }
    
    # we should never get to this point so...
    die('class.catFields.php - ERROR: RenderInput() couldn\'t find a legal input type!');
  }
}
?>
