<?php 

namespace autofight\Interfaces;

use autofight\BattleResult;

interface BattleLogger {

  const TYPE_HIT = 1;
  const TYPE_MISS = 2;
  const TYPE_DEATH = 3;
  const TYPE_MOVE = 4;
  const TYPE_INSANE = 5;

  function logOther($sMessage);

  function logResult(BattleResult $oResult);

  function logMultiple(array $aResults);
}


?>