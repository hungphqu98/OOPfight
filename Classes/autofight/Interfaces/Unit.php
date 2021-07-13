<?php 
namespace autofight\Interfaces;
use autofight\Army;

interface Unit {

  // Return remaining health
  function getHealth();

  // Increase health by given value
  function increaseHealth($iIncrease = null);

  // Decrease health by given value
  function decreaseHealth($iDecrease = null);

  // Return accuracy percentage
  function getAccuracy();
  
  // Perform action against the attacked army
  function act(Army $oAttackedArmy);

  // Return attack radius of unit
  function getRadius();

  // True if unit is alive, false otherwise 
  function isAlive();

  // Return type of unit
  function getType();

  // Return damage of unit
  function getDamage();

  // The chance of getting this unit in a random draw
  static function getRarity();

  // Set army for unit
  function setArmy(Army $oArmy);

  // Return the unit's army
  function getArmy();

  // Set the unit's position in array
  function setIndex($iIndex);

  // Return the unit's position in array
  function getIndex();

  // Echo unit in readable format
  function __toString();

}

?>