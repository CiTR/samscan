﻿<?php
echo '<meta http-equiv="Content-Type"
 content="text/html; charset=UTF-8">';

 
//folder to scan for new music to add
$music_import_dir = "../FOR SAM/library/";
$playlist_import_dir =  "../FOR SAM/playlist/";

//folder in which to put files (your digital library drive)
$library_root = "../Library/";



$sam_category = [
    'cancon' => 2,
    'femcon' => 3,
    'cancon femcon' => 4,
    'playlist' => 5,
    'cancon 2' => 23,
    'cancon 3' => 24
];


$target_db_name = "SAMDB";

$db_address = '';
$db_username = '';
$db_password = '';
// replace path prefix (if the SAM instance plays music from a network drive mapped differently then
// your server's mapping, specify here (or just copy $library_root):
// when imported into SAM DB, $library_root gets replaced by $sam_path_prefix

$sam_path_prefix = "L:/";
//$sam_path_prefix = $library_root;
$error = '';

$db = new mysqli($db_address, $db_username, $db_password);
if (mysqli_connect_error()) {
    print('Connect Error for djland db (' . mysqli_connect_errno() . ') '
        . mysqli_connect_error());
}



date_default_timezone_set('America/Vancouver');



