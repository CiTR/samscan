<?php
/**
 * Created by PhpStorm.
 * User: brad
 * Date: 1/30/15
 * Time: 4:24 PM
 */

require_once("getid3/getid3/getid3.php");

$getID3 = new getID3();


function execute_sql($db, $script){
global $error;
    $sqlArray = explode(';',$script);
    foreach ($sqlArray as $stmt) {
        if (strlen($stmt)>3 && substr(ltrim($stmt),0,2)!='/*') {
            $result = mysqli_query($db, $stmt);
            if (!$result) {
                $error .= ' (error #'.mysqli_errno($db).') ';
                $error .= mysqli_error($db);
                $error .= ' statement: '.$stmt;
                return $error;
            }
        }
    }
}

// EXPECTS GLOBALS
function process_dir($dir){
    global $count; // int value is 0 initially
    global $file_list; // array to populate with filename list
    global $error;

    $dir_contents = scandir($dir);

    foreach ($dir_contents as $key => $filename){

        // IF ITS A DIR, RECURSE
        if ( is_dir($dir.$filename) && substr($filename,0,1) != "." )  {

            if(process_dir($dir.$filename."/"))
            {

            } else {
                $error .= "  can't process file: ".$dir.$filename."   ";
                return false;
            }
        } else {

        }
        // IF ITS A MP3 FILE, ADD TO ARRAY
        if ( is_file($dir.$filename) && substr($filename,0,1) != "." ){

            if ( file_extension($filename) == "mp3" ) {

                $count++;
                $file_list []= $dir.$filename;

            } else {

            }
        } else {

        }
    }
    return true;


}


function extractFromTags($path_and_file){

    global $getID3;
    global $error;

    $ThisFileInfo = $getID3->analyze($path_and_file);

    if (!array_key_exists('id3v2',$ThisFileInfo['tags'])){
        $error .= ' <br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(skipped) no id3v2 tags found in file:.'.$path_and_file;
        return false;
    }
//    echo '<pre>';
//    print_r($ThisFileInfo);

    $tags = $ThisFileInfo['tags']['id3v2'];

    $song = [];
//    $song['filename'] = $path_and_file; <- different if using import script or recreate script
    $song['duration'] = 200;
    $song['comment'] = isset($tags['comment'][0])? $tags['comment'][0] : '';
    $song['artist'] = $tags['artist'][0];
    $song['title'] = $tags['title'][0];
    $song['album'] = $tags['album'][0];
    $song['track_number'] = $tags['track_number'][0];
    $song['year'] = isset($tags['year'][0])? $tags['year'][0] : '';
    $song['genre'] = isset($tags['genre'][0])? $tags['genre'][0] : '';
    $song['composer'] = isset($tags['composer'][0] )? $tags['composer'][0] : $song['artist'];
    $song['isrc'] = isset($tags['isrc'][0])? $tags['isrc'][0] : "";
    $song['mood'] = isset($tags['mood'][0]) ? $tags['mood'][0] : "";


    $song_track = explode('/', $song['track_number']);
    $song['track_number'] = intval($song_track[0]);

    $song_track = $song['track_number'];
    $song['id'] = ( intval($song['isrc']) * 100 ) + $song_track;
	
	$song['year'] = substr($song['year'],0,4);

    $song['duration'] = 1000 * $ThisFileInfo['playtime_seconds'];

    if( strripos($song['comment'],'ategory')){

        foreach(str_split($song['comment']) as $k => $letter){
            if ($letter == '3'){
                $song['category'] = 3;
                break;
            }
            if ($letter == '2'){
                $song['category'] = 2;
                break;
            }
            // else
        }

    } else {
        $song['category'] ='';
    }

	
//	$song['title'] = str_replace('\x','',
	

    return $song;
    /*
     Optional: copies data from all subarrays of [tags] into [comments] so
     metadata is all available in one location for all tag formats
     metainformation is always available under [tags] even if this is not called
    */
//                getid3_lib::CopyTagsToComments($ThisFileInfo);

//echo $ThisFileInfo['comments_html']['artist'][0]; // artist from any/all available tag formats
//echo $ThisFileInfo['tags']['id3v2']['title'][0];  // title from ID3v2
//echo $ThisFileInfo['audio']['bitrate'];           // audio bitrate

}

function correct_path_differences($string){
    global $sam_path_prefix;
    global $library_root;
    return str_replace($library_root,$sam_path_prefix,$string);
}

function trim_fields($song){

    $max_length = [
        'id' => 11,
        'filename' => 200,
        'duration' => 11,
        'artist' => 170,
        'title' => 255,
        'album' => 255,
        'track_number' => 11,
        'composer' => 100,
        'isrc' => 50,
        'year' => 4,
        'genre' => 20,
        'comment' => 10000, /// arbitrary
        'mood' => 50,
        'category' => 50

    ];

    foreach($song as $k => $v){
        $song[$k] = substr($v, 0,  $max_length[$k]);
    }

    if( substr($song['filename'],-4,4) != '.mp3'){
        $song['filename'] .= '.mp3';
    }

    return $song;
}


function ingest_song($db, $song, $playlist = false){



    global $target_db_name;
    global $database_error;

    $replaced = array();
    $replaced['artist'] = replace_accents($song['artist']);
    $replaced['title'] = replace_accents($song['title']);
    $replaced['album'] = replace_accents($song['album']);
    $replaced['composer'] = replace_accents($song['composer']);
    $replaced['genre'] = replace_accents($song['genre']);

    foreach($replaced as $i => $v){
        if (mb_detect_encoding($v) != 'ASCII'){
            $database_error .= ' '.$i.' ('.$v.') is not latin ('.mb_detect_encoding($v).') SAM hates non-latin. ';

        }

    }

    $query = "INSERT INTO `".$target_db_name."`.`songlist`
        (`filename`,
        `duration`,
        `artist`,
        `title`,
        `album`,
        `trackno`,
        `composer`,
        `ISRC`,
        `albumyear`,
        `genre`,
        `info`,
        `mood`)
        VALUES
        ('".
        mysqli_real_escape_string($db,mb_convert_encoding($song['filename'],'latin1') )."','".
        $song['duration']."','".
        mysqli_real_escape_string($db,$replaced['artist'])."','".
        mysqli_real_escape_string($db,$replaced['title'])."','".
        mysqli_real_escape_string($db,$replaced['album'])."','".
        $song['track_number']."','".
        mysqli_real_escape_string($db,$replaced['composer'])."','".
        $song['isrc']."','".
        $song['year']."','".
        mysqli_real_escape_string($db,$replaced['genre'])."','".
        mysqli_real_escape_string($db,$song['comment'])."','".
        $song['mood']."')";





    if( $database_error == '' && mysqli_query($db,$query) ){

        $song['id'] = mysqli_insert_id($db);
        echo 'imported into SAM: &#x2713;';
        return categories($song, $playlist);

    } else {

        $database_error .= ' '.mysqli_error($db).' query:'.$query.'. ';
        echo '&nbsp;&nbsp;&nbsp;&nbsp;imported into SAM: X';
        return false;
		}
}

function categories($song, $playlist = false){
    global $db;
    $content = array();

    $categories = explode(' ',$song['mood']);

    $cancon = false;
    $femcon = false;
    foreach($categories as $k => $v){
        if(strtolower(substr($v,0,3)) == 'can'){
            apply_category($song['id'], 'cancon');
            $cancon = true;
        }

        if(strtolower(substr($v,0,3)) == 'fem'){
            apply_category($song['id'], 'femcon');
            $femcon = true;
        }

        if($cancon && $femcon){
            apply_category($song['id'], 'cancon femcon');

        }
    }

    if ($cancon && ($song['category'] == 2) ){

        apply_category($song['id'], 'cancon 2');

    } else if ($cancon && ($song['category'] == 3) ){

        apply_category($song['id'], 'cancon 3');
    }

    if($playlist){
        apply_category($song['id'], 'playlist');
    }

    return true;
}

function apply_category($id, $category){
    global $sam_category;
    global $target_db_name;
    global $db;

    $cat_id = $sam_category[$category];

    $query = "INSERT INTO `".$target_db_name."`.`categorylist`
        (`songID`,`categoryID`,`sortID`)
        VALUES
        ('".
        $id."','".
        $cat_id."','".
        "0')";

    if ( mysqli_query($db, $query)){

        return true;
    }    else {

        echo mysqli_error($db).'    '.$query;
        return false;
    }
}



function file_extension($filename){
    $array = explode(".",$filename);
    return $array[count($array)-1];
}

function sanitize_for_filename($string){

    $string = substr($string, 0,  60);
	$string = replace_accents($string);
    $string = preg_replace("([^\w\s\d\-_~,;:\[\]\(\).])", '-', $string);
    $string = preg_replace("([\.:;¡‐…])", '-', $string);
	$string = preg_replace('/\t+/', ' ', $string);
	$string = preg_replace("/\r\n|\r|\n/", ' ', $string);

    return $string;
}
function replace_accents($string){

	$unwanted_array = array( '‐'=> '-', '“'=>"'", '”' => "'",  'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'AE', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
									'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
									'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
									'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
									'ö'=>'o', 'ø'=>'o', 'œ'=>'oe',  'ù'=>'u', 'ü'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );

    $outstring = strtr( $string, $unwanted_array );

    return $outstring;
}


/* RAW SONGLIST INSERT

(`ID`,
`filename`,
`diskID`,
`flags`,
`songtype`,
`status`,
`weight`,
`balance`,
`date_added`,
`date_played`,
`date_artist_played`,
`date_album_played`,
`date_title_played`,
`duration`,
`artist`,
`title`,
`album`,
`label`,
`pline`,
`trackno`,
`composer`,
`ISRC`,
`catalog`,
`UPC`,
`feeagency`,
`albumyear`,
`genre`,
`website`,
`buycd`,
`info`,
`lyrics`,
`picture`,
`count_played`,
`count_requested`,
`last_requested`,
`count_performances`,
`xfade`,
`bpm`,
`mood`,
`rating`,
`overlay`,
`playlimit_count`,
`playlimit_action`,
`songrights`,
`adz_listID`)
VALUES
(".$song['id'].",
".$song['filename'].",
<{diskID: 0}>,
<{flags: NNNNNNNNNN}>,
<{songtype: S}>,
<{status: 0}>,
<{weight: 50}>,
<{balance: 0}>,
<{date_added: }>,
<{date_played: }>,
<{date_artist_played: 2002-01-01 00:00:01}>,
<{date_album_played: 2002-01-01 00:00:01}>,
<{date_title_played: 2002-01-01 00:00:01}>,
<{duration: 0}>,
<{artist: }>,
<{title: }>,
<{album: }>,
<{label: }>,
<{pline: }>,
<{trackno: 0}>,
<{composer: }>,
<{ISRC: }>,
<{catalog: }>,
<{UPC: }>,
<{feeagency: }>,
<{albumyear: 0}>,
<{genre: }>,
<{website: }>,
<{buycd: }>,
<{info: }>,
<{lyrics: }>,
<{picture: }>,
<{count_played: 0}>,
<{count_requested: 0}>,
<{last_requested: 2002-01-01 00:00:01}>,
<{count_performances: 0}>,
<{xfade: }>,
<{bpm: 0}>,
<{mood: }>,
<{rating: 0}>,
<{overlay: no}>,
<{playlimit_count: 0}>,
<{playlimit_action: none}>,
<{songrights: broadcast}>,
<{adz_listID: 0}>);



*/
