<?php

require_once("CONFIG.php");
require_once("helpers.php");
//above script reads $target_db_name and populates $creation_script

if(isset($_GET['import_only']) && ($_GET['import_only'] == 'true') ) {
    $import_without_moving = true;
} else {
    $import_without_moving = false;
}
// this array is populated by the recursive call below
$file_list = [];

if (!$import_without_moving && !is_dir($library_root)){
    echo 'not a dir: '.$library_root.'<br/>';
//	return;
}

if (!$import_without_moving && !is_writable($library_root) ){
    echo 'not writeable: '.$library_root.'<br/>';
    echo 'user: '.get_current_user();
    return;
}

process_dir($music_import_dir);

$count = 0;
foreach($file_list as $k => $file){

    $song = extractFromTags($file);

// echo $new_file.'<br/>';

    if ($import_without_moving){
        $song['filename'] = $file;
        // JUST IMPORT
        if (ingest_song($db,$song) ){
            echo 'successfully imported '.$file.'.<br/>';
        }  else {
            echo 'problem: '.mysqli_error($db);
        }

    } else {
        // COPY AND IMPORT

        $safe_artist = sanitize_for_filename($song['artist']);
        $safe_album = sanitize_for_filename($song['album']);
        $safe_title = sanitize_for_filename($song['title']);

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

        if(!is_dir($new_path) ) mkdir($new_path, 777, true);

        echo copy($file, $new_path . $new_file) . "|" . $new_file . '<br/>';

        if( file_exists ($new_path.$new_file) && ( filesize($new_path.$new_file) == filesize($file) ) )

            {
    //		unlink($file); // deleting doesn't work for some reason (permissions / security
                if (ingest_song($db,$song) ) echo 'safe to delete '.$file.'.<br/>';
                echo $song['artist'].' - '.$song['title'].' ('.$song['composer'].') <br/>';
            }
    }

	$count++;
}

echo '<hr/>processed '.$count.' files';

?>