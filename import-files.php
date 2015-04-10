﻿<?php

require_once("CONFIG.php");
require_once("helpers.php");
//above script reads $target_db_name and populates $creation_script

$playlist = (isset($_GET['playlist']) && $_GET['playlist'] == 'true');
if($playlist){
    $music_import_dir = $playlist_import_dir;
}

if(isset($_GET['import_only']) && ($_GET['import_only'] == 'true') ) {
    $import_without_moving = true;
} else {
    $import_without_moving = false;
}

if (!$import_without_moving && !is_dir($library_root)){
    echo 'not a dir: '.$library_root.'<br/>';
//	return;
}

if (!$import_without_moving && !is_writable($library_root) ){
    echo 'not writeable: '.$library_root.'<br/>';
    echo 'user: '.get_current_user();
    return;
}

echo 'scanning'.$music_import_dir.'...<br/>';
// this array is populated by the recursive call below
$file_list = [];

if (process_dir($music_import_dir))
{
    // great!
    echo 'found '.count($file_list).' mp3 files to scan...<br/>';
}
else {
    echo $error;
	return;
};

$count = 0;
$imported = 0;
$copied = 0;

$copy_problems = array();
$import_problems = array();

foreach($file_list as $k => $file){

    $song = extractFromTags($file);

    if ($error == '' && $import_without_moving){
        $song['filename'] = $file;
        // JUST IMPORT
        if (ingest_song($db,$song, $playlist) ){
            echo 'successfully imported '.$file.'.<br/>';
        }  else {
            echo 'problem: '.mysqli_error($db).'<br/>';
        }

    } else if ($song && ($error == '') ) {
        // COPY AND IMPORT

        $safe_artist = sanitize_for_filename($song['artist']);
        $safe_album = sanitize_for_filename($song['album']);
        $safe_title = sanitize_for_filename($song['title']);

//        echo '~'.mb_detect_encoding($song['artist']).'~';

        $new_path = $library_root
            . substr( $safe_artist , 0, 1)
            ."/"
            . trim( substr( $safe_artist, 0, 2) )
            ."/"
            . $safe_artist
            ."/"
            . $safe_album
            ."/";

        $new_file   = str_pad($song['track_number'],2,'0',STR_PAD_LEFT)
            ." "
            .$safe_artist
            ." - "
            .$safe_title
            .".mp3";

        $song['filename'] = $new_path.$new_file;

        $song = trim_fields($song);

        if(!is_dir($new_path) ) mkdir($new_path, 0777, true);

		if ( !file_exists ($song['filename']) || !( filesize($song['filename']) == filesize($file) ) ){
			
			if( file_exists($file) && is_readable($file) ){
            echo '<br/>copying '.$file.' to '.$song['filename'].'<br/>';
            $copied_yes = copy($file, $song['filename']);
            if (!$copied_yes) echo ' ***problem copying '.$file.' to '.$song['filename'].'. ';
				}
				else {
				    echo '<h3>could not copy - source file does not exist</h3>';
				}

		} else {
			echo '<br/>--- file already exists: '.$new_path.$new_file.'...';
		}
        if( file_exists ($song['filename']) && ( filesize($song['filename']) == filesize($file) ) )
            {
			echo '<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;file copied: &#x2713;';
			$copied++;
    //		unlink($file); // deleting doesn't work for some reason (permissions / security


      $song['filename'] = correct_path_differences($song['filename']);


			$database_error = '';
        if (ingest_song($db,$song, $playlist) ){
					echo '  (ok to delete source file) <br/> ';
					$imported++;
					}
        else {
					$import_problems []= $song['filename'].' could not be imported into DB: '.$database_error;
					}
            } else {
            echo '<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;file copied: X';

            $copyproblem = '';

			      $copyproblem.= 'Problem copying file "'.$file.'" to '.$song['filename'];
            $copyproblem.= '. Does the song exist at destination? (';
            $copyproblem.= file_exists ($song['filename'])? 'yes':'no';
            $copyproblem.= ')';
            $copyproblem.= ' What is the ';
            $copyproblem.= 'filesize of destination file?('.filesize($song['filename']).')  What about the filesize of the source?('.filesize($file);
            $copyproblem.= ')';
            $copyproblem.= '. Is the destination folder writable? (';
            $copyproblem.= is_writable($new_path)? 'yes':'no';
            $copyproblem.= ')  ';
    				$copy_problems []= $copyproblem;
			}

        $count++;
    }
    else {

        $copy_problems []= $error;
        $error = '';

    }

}
echo '<hr/><h3>folder contains '.count($file_list).' mp3 files</h3>';
echo '<hr/><h3>examined '.$count.' files</h3>';
echo '<hr/><h3>copied '.$copied.' files (or already existed)</h3>';
echo '<hr/><h3>imported '.$imported.' files into SAM</h3>';


$report = "\n\n\n**************************\n log from ".date('F j, Y - h:i:s a').":";

if( ($count != $copied) || ($copied != $imported) ){


    $report .= "\n";
    $report .= "\ncopy problems:\n";
    $report .= print_r($copy_problems, true);
    $report .= "\nimport problems:\n";
	  $report .= print_r($import_problems, true);



} else {
    $report .= 'no problems found!';
}


    if(file_exists($music_import_dir.'../log.txt')){
        if (is_writable($music_import_dir.'../log.txt')) {

            $logfile = $music_import_dir.'../log.txt';

            $log_contents = file_get_contents($logfile);

            file_put_contents($logfile, $report.$log_contents );

            echo 'report logged:<br/>';

        } else {
            echo 'log file not writable by user:'.get_current_user().'<br/>';
        }
    } else {
        echo 'log file not found.<br/>';
    }

    echo '<pre>'.$report;
?>