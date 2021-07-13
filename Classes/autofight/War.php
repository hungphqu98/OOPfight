<?php 

namespace autofight;

use autofight\Interfaces\BattleLogger;
use autofight\Interfaces\Unit;

class War {
  
  protected $aArmier = array();

  protected $oLogger;

  protected $iTurns = 0;

  // Set logger for battle output 
  public function setLogger(BattleLogger $oLogger) {
    $this->oLogger = $oLogger;
    return $this;
  }

  // Add an army to the battle  
  public function addArmy(Army $oArmy) {
    if ($oArmy->getLabel() === null) {
      $oArmy->setLabel($oArmy->generateRandomLabel());
    }
    $this->aArmies[] = $oArmy;
    return $this;
  }

  // Fight action
  public function fight() {
    if (count($this->aArmies) < 2) {
      die('War. War never changes. ... And as such it needs at least 2 armies!');
    }

    // if more than one army alive then do another turn
    while ($this->moreThanOneAliveArmy()) {
      $this->doTurn();
    }
    // game over if not
    $this->oLogger->logOther($this->getSurvivingArmy()->getLabel() . ' wins!');
  }

  // Processes turns and log messages 
  protected function doTurn() {
    $this->iTurns++;

    shuffle($this->aArmies);

    $this->oLogger->logOther('~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~');
    $this->oLogger->logOther('Turn ' . $this->iTurns . ' begins.');

    foreach ($this->aArmies as $i => $oArmy) {
      $this->oLogger->logOther('Army ' . $oArmy->getLabel() . ' goes ' . ordinal($i + 1));
    }

    foreach ($this->aArmies as $oArmy) {
      
      if ($this->moreThanOneAliveArmy()) {

        $oAttackedArmy = $this->findAttackableArmy($oArmy);
        $this->oLogger->logOther('Army "' . $oArmy->getLabel() . '" attacks "' . $oAttackedArmy->getLabel() . '".');
      };

      foreach ($oArmy->getUnits() as $oUnit) {
        if ($oUnit->isAlive()) {
          $aResults = $oUnit->act($oAttackedArmy);
          if (!empty($aResults)) {
            $aResults[] = '~';
          }
          $this->oLogger->logMultiple($aResults);
        }
      }
    }

  }

  // Check if more than one army alive
  protected function moreThanOneAliveArmy() {
    $iAlive = 0;

    foreach ($this->aArmies as $oArmy) {
      $iAlive += (int)(bool)$oArmy->countAlive();
    }
    return $iAlive > 1;
  }

  // Return the surviving army
  protected function getSurvivingArmy() {
    foreach ($this->aArmies as $oArmy) {
      if ($oArmy->countAlive()) {
        return $oArmy;
      }
    }
    return null;
  }

  // Find army to be attacked
  protected function findAttackableArmy(Army $oArmy) {
    $aAttackable = array();

    foreach ($this->aArmies as $oAvailableArmy) {
      if ($oAvailableArmy->getLabel() != $oArmy->getLabel() && $oAvailableArmy->countAlive()) {
        $aAttackable[] = $oAvailableArmy;
      }
    }
    if (isset($aAttackable[rand(0, count($aAttackable) - 1)])) {
      return $aAttackable[rand(0, count($aAttackable) - 1)];
    } else {
      die('Could not find any attackable army. Looks like ' . $oArmy->getLabel() . ' wins.');
    }
    
    $this->oLogger->logOther('~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~');
  }
  
}

?>