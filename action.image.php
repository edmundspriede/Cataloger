<?php
if( !defined('CMS_VERSION') ) exit;

$handlers = ob_list_handlers();
for ($cnt = 0; $cnt < sizeof($handlers); $cnt++) { ob_end_clean(); }

$spec = isset($_GET['i']) ? $_GET['i'] : '';
$debug = isset($_GET['debug']);
$anticache = isset($_GET['ac']);
$spec = preg_replace('/(\.\.|\/)/', '', $spec);
$config = cmsms()->GetConfig();

// not a jpg?
if( !preg_match('/\.jpg$/i', $spec) )
{
  $fn = returnMissing($config['root_path'], true, $debug);
  
  if( file_exists($fn) )
  {
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-cache, cachehack=".time());
    header("Cache-Control: no-store, must-revalidate");
    header("Cache-Control: post-check=-1, pre-check=-1", false);
    header('Content-Type: image/jpeg');
    $filedata = @file_get_contents($fn);
    echo $filedata;
    die();
  }
}

$fn = cms_join_path($config['uploads_path'], catFilesOperations::getAssetPath('i'), $spec); 
  
if($debug) error_log("Checking on " . $fn);
$sized = @stat($fn);

$spec = substr($spec, 0, strrpos($spec, '.'));
$parts = explode('_', $spec);
$parts = array_reverse($parts);
  
$showMissing = $parts[0] == '1';
$size = $parts[1];
$imgno = $parts[2];
$type = $parts[3];
$name = '';

for($j = count($parts) - 1; $j > 3; $j--)
{
  $name .= $parts[$j] . '_';
}

$srcSpec = cms_join_path(
                          $config['uploads_path'],
                          catFilesOperations::getAssetPath('s'),
                          $name 
                        );

$srcSpec .= 'src_' . $imgno . '.jpg';


if ($debug)
{
  error_log("CatalogerImage: src image " . $srcSpec);
}

$orig = @stat($srcSpec);

$newImage = false;

if($orig === false)
{
  if ($debug) error_log("Can't find " . $srcSpec);
  $fn = returnMissing($config['root_path'], $showMissing);
  
  if( file_exists($fn) )
  {
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-cache, cachehack=".time());
    header("Cache-Control: no-store, must-revalidate");
    header("Cache-Control: post-check=-1, pre-check=-1", false);
    header('Content-Type: image/jpeg');
    $filedata = @file_get_contents($fn);
    echo $filedata;
    die();
  }
  
}

if(!$sized || $sized['mtime'] < $orig['mtime'])
{
  if($debug) error_log("newer than existent, transforming");
  
  // we don't have a cached version we can use
  $destSpec = $config['uploads_path'].$this->getAssetPath('i').'/'.$spec.'.jpg';
  // so we make one
  imageTransform($srcSpec, $destSpec, $size, $config);
  $newImage = true;
}

//$dest = "Location: ".$config['uploads_url'].
//  $this->getAssetPath('i').'/'.$spec.'.jpg';
  
$fn = $config['uploads_path'] .
  $this->getAssetPath('i').'/'.$spec.'.jpg';

if($newImage || $anticache)
{
  if( file_exists($fn) )
  {
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-cache, cachehack=".time());
    header("Cache-Control: no-store, must-revalidate");
    header("Cache-Control: post-check=-1, pre-check=-1", false);
    header('Content-Type: image/jpeg');
    $filedata = @file_get_contents($fn);
    echo $filedata;
    die();
  }
}
//
//if ($debug) error_log($dest);
//
//header($dest);
//die;

if( file_exists($srcSpec) )
{
  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
  header("Cache-Control: no-cache, cachehack=".time());
  header("Cache-Control: no-store, must-revalidate");
  header("Cache-Control: post-check=-1, pre-check=-1", false);
  header('Content-Type: image/jpeg');
  $filedata = @file_get_contents($srcSpec);
  echo $filedata;
  die();
}

function returnMissing($rootUrl, $showMissing, $debug=false)
{
  // if so desired, don't 404, but send an image
  if ($debug)
    error_log("CatalogerImage: no image at $rootUrl");
  
  if (! $showMissing) 
    return $rootUrl . '/modules/Cataloger/images/trans.gif';
  else
    return $rootUrl . '/modules/Cataloger/images/no-image.gif';      
}

/*
  to be moved
 */
function imageTransform($srcSpec, $destSpec, $size, &$config, $aspect_ratio='')
{
  $mod = cmsms()->GetModuleInstance('Cataloger');
  $fn = cms_join_path(
                        $mod->GetModulePath(),
                        'lib',
                        'external',
                        'Image',
                        'Transform.php'
                      );
  
  require_once($fn);

  $it = new Image_Transform;
  $img = $it->factory('GD');
  $img->load($srcSpec);
  
  if ($img->img_x < $img->img_y)
  {
    $long_axis = $img->img_y;
  }
  else
  {
    $long_axis = $img->img_x;
  }

  if ($long_axis > $size)
  {
    $img->scaleByLength($size);
    $img->save($destSpec, 'jpeg');
  }
  else
  {
    $img->save($destSpec, 'jpeg');
  }
  
  $img->free();
}
?>