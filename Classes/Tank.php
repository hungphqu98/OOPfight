<?php 

use autofight\Abstracts\Unit as aUnit;
use autofight\Interfaces\Unit as iUnit;

class Tank extends aUnit
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
}

?>