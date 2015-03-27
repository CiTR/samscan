<?php

require_once('CONFIG.php');
require_once('helpers.php');
$string = "Little Darla's dog ?!@#$%^&*(){}[]\||\:::...;;éêel´p´hí´cçeçåcáâ;. “Skip”./////\\\\:;' Has A Treat For You, Volume 23: Summer 2005";

$nu = 'nü';

/*
echo "before:<br/>".$string;
echo "after:<br/>".sanitize_for_filename($string);

echo '<hr/>';
echo $nu;
echo '<hr/>LATIN1 ';
echo mb_convert_encoding($nu,'latin1');
echo '<hr/>unpack H*: ';
echo pack('H*', 'nü');
echo '<hr/>';
echo $nu;
echo '<hr/>';
echo $nu;
echo '<hr/>';
echo $nu;
*/

mkdir('/Users/brad/Music/Ltest/dingus', 0777, true);
?>