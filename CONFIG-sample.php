<?php

 
//folder to scan for new music to add
$music_import_dir = "../Test-ingest/";

//folder in which to put files (your digital library drive)
$library_root = "../Test-lib/";


$target_db_name = "SAMreconstructtest2";

$db_address = '';
$db_username = '';
$db_password = '';

$db = new mysqli($db_address, $db_username, $db_password);
if (mysqli_connect_error()) {
    print('Connect Error for djland db (' . mysqli_connect_errno() . ') '
        . mysqli_connect_error());
}







