<?php 

namespace autofight;
use autofight\Interfaces\Unit;

class Army {

  protected $iSize;

  protected $sLabel;

  protected $aUnits = array();

  // Default label
  protected static $aAdjectives = ['Iron', 'Fuchsia', 'Red', 'Brave', 'Lonely', 'Bitter', 'Deadly', 'Black', 'Armored'];
  protected static $aNouns = ['Scorpions', 'Hand', 'Death', 'Marauders', 'Itch', 'Scratch', 'Zeus', 'Hummer', 'Volcano'];

  // Generating army
  public function __construct($iSize) {
    $this->setSize((int)$iSize);
    $this->buildArmy();
  }

  // Build army
  protected function buildArmy() {
    return $this;
  }

  // Set army size
  protected function setSize($iSize) {
    if (!is_numeric($iSize)|| $iSize < 1) {
      die('Army construct param needs to be numeric and positive."' . $iSize . '" is not.');
    }

    $this->iSize = $iSize;
    return $this;
  }

  // Get army size
  public function getSize() {
    return $this->iSize;
  }

  // Set army label
  public function setLabel($sLabel) {
    if (!is_string($sLabel) && !is_numeric($sLabel)) {
      die('A label must be a string or a number. "' . $sLabel . '" given.');
    }
    $this->sLabel = $sLabel;
    return $this;
  }

  // Get army label
  public function getLabel() {
    return $this->sLabel;
  }

  // Create random label
  public function generateRandomLabel() {
    if (empty(self::$aAdjectives) || empty(self::$aNouns)) {
      return (string)rand(0, 1000);
    }

    $iAdjective = rand(0, count(self::$aAdjectives) - 1);
    $iNoun = rand(0, count(self::$aNouns) - 1);
    $sLabel = self::$aAdjectives[$iAdjective] . ' ' . self::$aNouns[$iNoun];

    // Remove picked values
    unset(self::$aAdjectives[$iAdjective], self::$aNouns[$iNoun]);
    self::$aNouns = array_values(self::$aNouns);
    self::$aAdjectives = array_values(self::$aAdjectives);

    return $sLabel;
  }

  // Get units
  public function getUnits() {
    return $this->aUnits;
  }

  // Count number of alive units
  public function countAlive() {
    $i = 0;
    foreach ($this->aUnits as $oUnit) {
      $i += (int)$oUnit->isAlive();
    }
    return $i;
  }

  // Get a random alive unit
  public function getRandomAliveUnit(Unit $oNotUnit = null) {
    $aAliveUnits = array();
    foreach ($this->aUnits as $oUnit) {
      if ($oUnit->isAlive()) {
        if (!$oNotUnit || ($oNotUnit && $oNotUnit->getIndex() != $oUnit->getIndex())) {
          $aAliveUnits[] = $oUnit;
        }
      }
    }
    $i = rand(0, count($aAliveUnits) - 1);
    return (isset($aAliveUnits[$i])) ? $aAliveUnits[$i] : null;
  }

}

?>