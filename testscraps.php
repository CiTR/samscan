<?php

require_once('CONFIG.php');
require_once('helpers.php');
$string = "Little Darla's dog ?!@#$%^&*(){}[]\||\:::...;;éêel´p´hí´cçeçåcáâ;. “Skip”./////\\\\:;' Has A Treat For You, Volume 23: Summer 2005";

echo "before:<br/>".$string;
echo "after:<br/>".sanitize_for_filename($string);

$file_list = [];

process_dir($music_import_dir);

$count = 0;
$file = $file_list[0];

    $song = extractFromTags($file); 
	
	$a_title = 'Canção de Amor';
	$a_title = 'Café Tacuba';
	echo "<pre>";
	 
	
	
	echo "a_title: ".$a_title."\n";
	echo "$song[title]: ".$song["title"]."\n";
	echo "Canção de Amor: "."Canção de Amor"."\n";
	$temp = $song['title'];
	echo "temp: ".$temp."\n";
	
	echo "\n\nACCENTS REPLACED (should replace accented chars with readible subs, but doesn't when inside array:\n";
	echo "$a_title: ".replace_accents($a_title)."\n";
	echo "$temp: ".replace_accents($temp)."\n";
	echo "$song[title]: ".replace_accents($song['title'])."\n";
	echo "Canção de Amor: ".replace_accents("Canção de Amor")."\n";
	
	
?>