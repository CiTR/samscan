﻿<?php

require_once("CONFIG.php");
require_once("helpers.php");
//above script reads $target_db_name and populates $creation_script

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

// this array is populated by the recursive call below
$file_list = [];
process_dir($music_import_dir);

$count = 0;
$imported = 0;
$copied = 0;

$copy_problems = [];
$import_problems = [];

foreach($file_list as $k => $file){

    $song = extractFromTags($file);

// echo $new_file.'<br/>';

    if ($import_without_moving){
        $song['filename'] = $file;
        // JUST IMPORT
        if (ingest_song($db,$song) ){
            echo 'successfully imported '.$file.'.<br/>';
        }  else {
            echo 'problem: '.mysqli_error($db).'<br/>';
        }

    } else {
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

        if(!is_dir($new_path) ) mkdir($new_path, 0777, true);
		
		if ( !file_exists ($song['filename']) || !( filesize($song['filename']) == filesize($file) ) ){
			$song = trim_fields($song);
			echo '<br/>--- copying '.$song['filename'].'...';
			
			if( file_exists($file) && is_readable($file) ){
				copy($file, $song['filename']);
			//	echo 'copy '.$file.' to '.$song['filename'].'<br/>';
				}
				else {
				echo '<h3>could not copy - file does not exist</h3>';
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
                if (ingest_song($db,$song) ){
					echo ' imported (can delete) <br/> ';
					$imported++;
					} else {
					$import_problems []= $song['filename'].' could not be imported into DB: '.$database_error;
					}
            } else {
			echo '<h3>problem:exists?('.file_exists ($song['filename']).')||filesize of dest?('.filesize($song['filename']).')||filesize of source?('.filesize($file).')||';
			
				$copy_problems []= $file.' could not be copied to library folder';
			}
    } 

	$count++;
}

echo '<hr/><h3>examined '.$count.' files</h3>';
echo '<hr/><h3>copied '.$copied.' files (or already existed)</h3>';
echo '<hr/><h3>imported '.$imported.' files</h3>';

if( ($count != $copied) || ($copied != $imported) ){
	echo '<hr/><pre>';
	print_r($import_problems);
	print_r($copy_problems);
}
?>