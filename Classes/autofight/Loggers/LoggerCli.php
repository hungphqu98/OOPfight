<?php 

namespace autofight\Loggers;

use autofight\BattleResult;
use autofight\Interfaces\BattleLogger;

class LoggerCli implements BattleLogger {

  protected $aPrefixes = array(
    BattleLogger::TYPE_HIT => 'Hit',
    BattleLogger::TYPE_MISS => 'Miss',
    BattleLogger::TYPE_DEATH => 'Death',
    BattleLogger::TYPE_MISS => 'Move',
    BattleLogger::TYPE_INSANE => 'Insanity'
  );

  protected $aColors = array(
    BattleLogger::TYPE_HIT => '0;32m',
    BattleLogger::TYPE_MISS => '0;31m',
    BattleLogger::TYPE_DEATH => '0;33m',
    BattleLogger::TYPE_MOVE => '0;37m',
    BattleLogger::TYPE_INSANE => '0;36m'
  );

  function logOther($sMessage) {
    print $sMessage.PHP_EOL;
    usleep(500000);
    return $this;
  }

  function logResult(BattleResult $oResult) {
    $sMessage = "\033[".$this->aColors[$oResult->type].' '.$this->aPrefixes[$oResult->type].'! ';
    $sMessage .= $oResult->attacker.' ';
    switch ($oResult->type) {
      case (BattleLogger::TYPE_HIT) :
        $sMessage .= $oResult->message.'. '.ucfirst($oResult->defender);
        $sMessage .= ' takes '.$oResult->amount.' damage.';
        break;
      case (BattleLogger::TYPE_MISS) : 
        $sMessage .= $oResult->message.'. '.ucfirst($oResult->defender);
        $sMessage .= ' is safe.';
        break;
      case (BattleLogger::TYPE_DEATH) :
        $sMessage .= 'causes '.$oResult->amount.' damage and '.$oResult->message.' '.ucfirst($oResult->defender).'!!';
        break;
      default:
        break;
    }
    print $sMessage."\033[".$this->aColors[$oResult->type]." \033[1;37m".PHP_EOL;
    usleep(500000);
    return $this;
  }

  function logMultiple(array $aResults) {
    foreach ($aResults as $oResult) {
      if (is_string($oResult)) {
        $this->logOther($oResult);
      } else if ($oResult instanceof BattleResult) {
        $this->logResult($oResult);
      }
    }
    return $this;
  }

}
?>