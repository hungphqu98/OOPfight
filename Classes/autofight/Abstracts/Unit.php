<?php 
namespace autofight\Abstracts;
use autofight\Army;

abstract class Unit implements \autofight\Interfaces\Unit
{
  protected $aMessages = array() ;

  protected $iHealth;

  protected $iMaxHealth;

  protected $iAccuracy;

  protected $iRadius;

  protected $sType;

  protected $iDamage;

  protected $oArmy;
 
  protected $iIndex;

  // Return accuracy
  function getAccuracy() {
    return $this->iAccuracy;
  }

  // Return remaining health
  function getHealth() {
    return $this->iHealth;
  }

  // Increase health
  function increaseHealth($iIncrease = null) {
    $this->iHealth += ($iIncrease === null) ? : abs((int)$iIncrease);
    return $this;
  }

  // Decrease health 
  function decreaseHealth($iDecrease = null) {
    $this->iHealth += ($iDecrease === null) ? : abs((int)$iDecrease);
    return $this;
  }

  // Return true if unit alive, false otherwise
  function isAlive() {
    return !($this->iHealth <= 0);  
  }

  // Return attack radius
  function getRadius() {
    return $this->iRadius;
  }

  // Return type of unit
  function getType() {
    return $this->sType;
  }

  // Return unit damage
  function getDamage() {
    return $this->iDamage;
  }

  // Set army for unit
  function setArmy(Army $oArmy) {
    $this->oArmy = $oArmy;
    return $this;
  }

  // return the unit's army
  function getArmy() {
    return $this->oArmy;
  }

  // set unit's position
  function setIndex($iIndex) {
    $this->iIndex = (int)$iIndex;
    return $this;
  }

  // return unit's position
  function getIndex() {
    return $this->iIndex;
  }

  // Return message from message property
  function determineMessage($iHitScore) {
    foreach ($this->aMessages as $iScore => $aMessages) {
      if ($iHitScore > $iScore) {
        continue;
      } else {
        return $aMessages[rand(0,count($aMessages)-1)];
      }
    }
    return '-->';
  }

  // Return a random element
  protected function getRandomElement(array $aArray) {
    if (!empty($aArray)) {
      return $aArray[rand(0,count($aArray)-1)];
    }
    return null;
  }

  // Return unit name,index & army name
  function __toString() {
    return ucfirst($this->getType()).' unit '.$this->getIndex().' ('.$this->getArmy()->getLabel().')';
  }


}


?>