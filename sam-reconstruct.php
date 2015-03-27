
<?php
require_once("CONFIG.php");
require_once("helpers.php");
require_once("buildscript.php");
//above script reads $target_db_name and populates $creation_script

$error = '';
$start_time = time();

$result = execute_sql($db, $creation_script);

if ($result == '') {
    echo "database '".$target_db_name."' created succesfully! Now adding music...<hr>";

} else {
    $error .= "An error occurred during db creation! ".mysqli_error($db);

}

is_dir($library_root)? : $error .= "source directory cannot be found.  Looking for '".$library_root."'.  ";
is_readable($library_root)? : $error .= "source directory not readable: '".$library_root."'.  ";

// writeable test returns false positive (returns true when it is actually writeable)
//	is_writable($destination)? : $error .= "destination directory cannot be written to.  Trying to write to '".$destination."'.  ";

$count = 0;

if($error == '') {
    $start_time = time();
    $file_list = [];
    process_dir($library_root); //populates file_list array

    foreach ($file_list as $k => $file){

        $song = extractFromTags($file);

        $arr = mbStringToArray($file);

        echo '<pre>';
        echo mb_detect_encoding($file);
        print_r($arr);
        echo'</pre>';
		    $song['filename'] = $file;
		
        $result = ingest_song($db, $song);
        if ( $result == 1 ){

            echo $song['artist']." - ".$song['title']." ingested successfully <br/>";
        } else {
            echo mysqli_error($db);
        }
    }

    $end_time = time();
    $diff = $end_time - $start_time;
    echo 'took '.$diff.' seconds to scan '.$count.' files';

} else {
    echo $error;
}





function mbStringToArray ($string) {
    $array = [];
    $strlen = strlen($string);
    while ($strlen) {
        $array[] = substr($string,0,1);
        $string = substr($string,1,$strlen);
        $strlen = strlen($string);
    }
    return $array;
}
