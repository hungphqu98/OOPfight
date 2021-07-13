<?php 
function ordinal($i)
{
    $aEndings = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
    if (($i % 100) >= 11 && ($i % 100) <= 13) {
        $n = $i . 'th';
    } else {
        $n = $i . $aEndings[$i % 10];
    }
    return $n;
}

?>