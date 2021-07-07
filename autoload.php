<?php 

function autoload ($sClassName) {
  $sPath = str_replace('\\','/', $sClassName);
  if (is_readable('Classes/'.$sPath.'.php')) {
    require_once 'Classes/'.$sPath.'.php';
  } else {
    die('Class '.$sClassName.' not found.');
  }
}

spl_autoload_register('autoload');

?>