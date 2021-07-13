<?php 
namespace autofight;
use autofight\Abstracts\Unit as aUnit;
use autofight\Interfaces\Unit as iUnit;
use autofight\Interfaces\BattleLogger;

class Infantry extends aUnit
{
  protected $aMessages = array(
    1 => array(
        'brings eternal shame to his family',
        'critically misses',
        'shoots at the sky',
        'shoots himself in the foot',
        'jams his rifle',
        'has a bullet explode in his rifle',
        'breaks his rifle in half',
        'hits himself in the head with recoil'
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

  protected $aSuicideMessages = array(
    'grew tired of it all and decided to end it',
    'couldn\'t handle the killing',
    'didn\'t have the stomach for war',
    'gave up on life',
    'killed himself',
    'swallowed his own bullet',
    'sat on a grenade, pulled out the pin, and waited',
    'stepped on a land mine. On purpose'
  );
    
  protected $aIdleMessages = array(
    'didn\'t feel like participating',
    'went to sleep',
    'was too depressed to hold the rifle',
    'sat down and looked the other way',
    'started crying',
    'decided to clean his rifle',
    'went to call his wife',
    'couldn\'t stop looking at his girlfriend\'s picture',
    'went to grab something to eat'
  );

  protected $aFriendlyFireMessages = array(
    'went insane and attacked his own',
    'went crazy and aimed at his friend',
    'couldn\'t handle it and decided to attack the platoon leader',
    'became too depressed to aim at enemies, and chose friends instead',
    'decided to switch sides, for the time being',
    'went mad and switched sides temporarily'
  );

  protected static $rarity = 100;

  protected $iHealth = 100;

  protected $iMaxHealth = 100;

  protected $iAccuracy = 50;

  protected $iRadius = 1;

  protected $sType = 'infantry';

  protected $iDamage = 30;
  
  // Choose action 
  public function act(Army $oAttackedArmy) {
    
    $aResults = array();
    $oResult = new BattleResult();
    $oResult->attacker = $this;
    $oResult->amount = 0;

    // Choose a random sabotage action if unlucky or else shoot at the chosen army 
    if (rand(1, 1000000) == 1) {
        $oResult->type = BattleLogger::TYPE_INSANE;
        switch (rand(0, 2)) {
            case 0:
                $this->iHealth = 0;
                $oResult->defender = $this;
                $oResult->message = $this->getRandomElement($this->aSuicideMessages);
                $oResult->amount = 1000;
                break;
            case 1:
                $oResult->defender = $this;
                $oResult->message = $this->getRandomElement($this->aIdleMessages);
                break;
            case 2:
                $aResults[] = $this. ' ' . $this->getRandomElement($this->aFriendlyFireMessages).'!';
                $oAttackedUnit = $this->getArmy()->getRandomAliveUnit($this);
                $aResults = array_merge($aResults, $this->shoot($oAttackedUnit));
                break;
            default:
                break;
        }
    } else {
        $oAttackedUnit = $oAttackedArmy->getRandomAliveUnit();
        if ($oAttackedUnit) {
            return $this->shoot($oAttackedUnit);
        } else {
            return array();
        }
    }
    
    $aResults[] = $oResult;
    return $aResults;
    
  }
  // Shoot action
  public function shoot(iUnit $oUnit) {
    $aResults = array(); 
    $oResult = new BattleResult();
    $oResult->attacker = $this;
    $oResult->defender = $oUnit;

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
                    $aResults[] = $this. ' aims at ' .$oUnit.' but bullet strays towards '.$oUnitToShoot.'!';
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
    
    $oResult->amount = $iAmount;

    // Decrease health of unit and checked if dead
    $oUnit->decreaseHealth($iAmount);
    if (!$oUnit->isAlive()) {
        $oResult->message = 'kills';
        $oResult->type = BattleLogger::TYPE_DEATH;
    }

    // Add message to log
    $aResults[] = $oResult;
    if (isset($sAddedMessage)) {
        $aResults[] = $sAddedMessage;
    }
    $aPostMerge = (isset($aPostMerge)) ? $aPostMerge : array();
    $aResults = array_merge($aResults, $aPostMerge);
    return $aResults;
      
  }
  // Get unit rarity
  static function getRarity() {
    return self::$rarity;
  }
}

?>