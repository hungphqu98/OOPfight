<?php 
namespace autofight;
use autofight\Abstracts\Unit as aUnit;
use autofight\Interfaces\Unit as iUnit;
use autofight\Interfaces\BattleLogger;

class Tank extends aUnit
{
  protected $aMessages = array(
    1 => array(
        'brings eternal shame to his family',
        'critically misses',
        'shoots at the sky',
        'has a projectile explode in the pipe',
        'breaks the tank tracks',
        'drops the projectile on the driver\'s head while reloading',
        'dents the turret',
        'cracks the turret on a tree'
    ),
    20 => array(
        'misses badly',
        'fails miserably',
        'shoots like a blind five year old'
    ),
    40 => array(
        'shoots clumsily',
        'misses by a yardstick',
        'appears to be seeing double'
    ),
    50 => array(
        'shoots too low',
        'shoots too high',
        'shoots the ground'
    ),
    60 => array(
        'slightly wounds',
        'grazes',
        'pokes'
    ),
    80 => array(
        'wounds',
        'hits',
        'pierces'
    ),
    99 => array(
        'hits well',
        'hits hard',
        'badly wounds'
    ),
    100 => array(
        'critically hits',
        'pulverizes',
        'destroys',
        'obliterates',
        'critically wounds'
    )
  );

  protected static $rarity = 10;

  protected $iHealth = 500;

  protected $iMaxHealth = 500;

  protected $iAccuracy = 35;

  protected $iRadius = 3;

  protected $iDamage = 50;

  protected $sType = 'tank';

  // Choose action
  public function act(Army $oAttackedArmy) {
    if ($oAttackedArmy->countAlive()) {
        return $this->shoot($oAttackedArmy->getRandomAliveUnit());
    }
    return array();
  }

  // Shoot action
  public function shoot(iUnit $oUnit) {
    $aResults = array(); 
    $oResult = new BattleResult();
    $oResult->attacker = $this;
    $oResult->defender = $oUnit;
    // 
    $aPostMerge = array();

    // Random a hit score
    $iHitScore = rand(1,100);
    $bHit = $iHitScore >= $this->iAccuracy;

    $oResult->type = ($bHit) ? BattleLogger::TYPE_HIT : BattleLogger::TYPE_MISS;
    $oResult->message = $this->determineMessage($iHitScore);

    // Get shot types based on hit score
    $fPercentageOfAccuracy = $iHitScore / $this->iAccuracy * 100;
    if (!$bHit) {
        $iAmount = 0;
        if ($fPercentageOfAccuracy > 50 && $fPercentageOfAccuracy < 60) {
            $aAdjacent = $this->getArmy()->getAdjacentUnits($this, 1);
            if (!empty($aAdjacent)) {
                $oUnitToShoot = $this->getRandomElement($aAdjacent);
                if ($oUnitToShoot) {
                    $aResults[] = $this. ' aims at' .$oUnit.' but the projectile strays towards '.$oUnitToShoot.'!';
                    $aPostMerge = $this->shoot($oUnitToShoot);
                }
            }
        } else if ($iHitScore == 1) {
            switch (rand(0, 1)) {
                case 0:
                  $this->iAccuracy = ($this->iAccuracy < 11) ? 1 : ($this->iAccuracy - 10);
                  $sAddedMessage = $this .' has suffered a permanent reduction of accuracy!';
                  break;
                case 1:
                  $this->iHealth = ($this->iHealth < 11) ? 1 : ($this->iHealth - 10);  
                  $sAddedMessage = $this .' has suffered a permanent reduction of health!';
                default:
                  break;
            }
        }
    } else {
        if ($iHitScore == 100) {
            $iAmount = $this->iDamage * 5;
            $aResults[] = $this.' scored a critical hit!!';
        } else {
            $iAmount = $this->iDamage * $iHitScore / 100;
        }
    }
    
    // Check for splash damage
    $aAdjacent = $oUnit->getArmy()->getAdjacentUnits($oUnit, $this->getRadius());
    $aPostMerge[] = 'Splash Damage!';

    foreach ($aAdjacent as $oAdjacentUnit) {
        if ($oAdjacentUnit->isAlive()) {
            $iAmountToReduce = round($iAmount * ($this->getRadius() - abs($oUnit->getIndex()-$oAdjacentUnit->getIndex())) / ($this->getRadius()*2) + 1, 2);
            $oAdjacentUnit->decreaseHealth($iAmountToReduce);
            if ($oAdjacentUnit->isAlive()) {
                $aPostMerge[] = $oAdjacentUnit.' was hit by shrapnel for '.$iAmountToReduce.' damage.';
            } else {
                $aPostMerge[] = $oAdjacentUnit.' was hit by shrapnel for '.$iAmountToReduce.' damage and perished.';
            }
        } else {
            $aPostMerge[] = 'The corpse of '.$oAdjacentUnit. 'is mutilated by shrapnel.';
        }
    }


    $oResult->amount = $iAmount;

    // Decrease health of unit and check if dead
    $oUnit->decreaseHealth($iAmount);
    if (!$oUnit->isAlive()) {
        $oResult->message = 'kills';
        $oResult->type = BattleLogger::TYPE_DEATH;
    }

    $aResults[] = $oResult;
    if (isset($sAddedMessage)) {
        $aResults[] = $sAddedMessage;
    }
    $aResults = array_merge($aResults, $aPostMerge);
    return $aResults;
    
}
  // Get unit rarity
  static function getRarity() {
      return self::$rarity;
  }
}

?>