<?php 
  require_once 'autoload.php';

  use autofight\Army;

  Army::addUnitType(new \autofight\Infantry());
  Army::addUnitType(new \autofight\Tank());

  $oArmy = new Army(50);
  $oArmy->setLabel($oArmy->generateRandomLabel());

  die(var_dump($oArmy));

?>