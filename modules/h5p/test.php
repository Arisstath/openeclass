<?php

// include '../../include/init.php';
$require_current_course = true;

require_once '../../include/baseTheme.php';

require_once 'H5Pclass.php';
require_once 'h5p-php-library/h5p.classes.php';
require_once "h5p-php-library/h5p-default-storage.class.php";
require_once "h5p-php-library/h5p-file-storage.interface.php";
require_once "h5p-php-library/h5p-development.class.php";
require_once "h5p-php-library/h5p-metadata.class.php";

function upload_content(){ // anebasma // na elen3w to database &  na balw to user_input

	global $course_id;
	global $course_code;
	global $webDir;

	$upload_dir = $webDir . '/courses/temp/h5p/' . $course_code;
	if(file_exists($upload_dir)){
		deleteDirectory($upload_dir);
		mkdir($upload_dir);
	}else{
		mkdir($upload_dir);
	}

	$target_file = $upload_dir . "/" . basename($_FILES["userFile"]["name"]);
	move_uploaded_file($_FILES["userFile"]["tmp_name"], $target_file);

	$classobj = new H5Pclass();
	$path = $webDir . '/modules/h5p/h5p';
	$url = $webDir . '/courses/temp/h5p/' . $course_code;
	$language = 'en';
	$classCore = new H5PCore($classobj, $path,$url,$language,FALSE);
	$classVal = new H5PValidator($classobj, $classCore);
	$classStor = new H5PStorage($classobj, $classCore);

	if($classVal->isValidPackage()){
		$classStor->savePackage();

		$content_id = "";
		$sql = Database::get()->querySingle("SELECT * FROM h5p_content WHERE course_id = ?d ORDER BY id DESC",$course_id);
		$content_id = $sql->id;

		$filesource = $webDir . "/modules/h5p/h5p/content/" . $content_id . "/h5p.json";

		$source = $webDir . "/modules/h5p/h5p/content/" . $content_id . "/extract";
		$something = scandir($source,1);
		$source = $source . "/" . $something[0];

		$dirr = $webDir . "/courses/" . $course_code . "/h5p";

		if(!file_exists($dirr)){
			mkdir($dirr);
			$dirc = $dirr . "/content";
			mkdir($dirc);
			$dirw = $dirr . "/workspace";
			mkdir($dirw);

		}

		$dest = $webDir . "/courses/" . $course_code . "/h5p/content/" . $content_id;
		if(!file_exists($dest)){
			mkdir($dest);
		}
		$filedest = $dest . "/h5p.json";
		if(copy($filesource, $filedest)){ 
			$dest = $dest . "/" . $something[0];
			$file = $webDir . "/courses/" .  $course_code . "/h5p/content/" . $content_id . "/h5p.json";
			$file = file_get_contents($file);
			$file = json_decode($file);
			var_dump($file->title);
			$sql = Database::get()->query("UPDATE h5p_content SET title = ?s WHERE id = ?d AND course_id = ?d", $file->title, $content_id, $course_id);
			return(copy($source, $dest));
		}else{
			return false;
		}
	}
}

function show_content($content_id){ 
	global $course_id;
	global $course_code;
	global $webDir;

	$workspace_dir = $webDir . "/courses/" . $course_code . "/h5p/workspace";
	$content_dir = $webDir . "/courses/" . $course_code . "/h5p/content/" . $content_id;
	$h5p = scandir($content_dir,1);
	$sentence_we_need = '.h5p';
	foreach($h5p as $h){
		if(strpos($h, $sentence_we_need)){
			$h5p = $h;
		}
	}
	deleteDirectory($workspace_dir);
	mkdir($workspace_dir);
	$content = $content_dir . "/" . $h5p;
	var_dump($content);
	var_dump($h5p);
	var_dump($workspace_dir);
	var_dump($content_dir);

	$zip = new ZipArchive;
	$res = $zip->open($content);
	if($res){
		$zip->extractTo($workspace_dir);
		if($zip->close()){
			return true;
		}else{
			return false;
		}
	}

}

function deleteDirectory($dir) { 
    if (!file_exists($dir)) {
      return true;
    }

    if (!is_dir($dir)) {
		return unlink($dir);
	}

	foreach (scandir($dir) as $item) {
		if ($item == '.' || $item == '..') {
		    continue;
		}

		if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
		    return false;
		}

	}

	return rmdir($dir);
}
function delete_content($content_id){
	global $course_id;
	global $course_code;
	global $webDir;
	$content_dir = $webDir . "/courses/" . $course_code . "/h5p/content/" . $content_id;
	deleteDirectory($content_dir);
	$sql = Database::get()->query("DELETE FROM h5p_content WHERE course_id = ?d AND id = ?d ",$course_id,$content_id);
	$content_dir_mod = $webDir . "/modules/h5p/h5p/content/" . $content_id;
	var_dump($content_dir);
	var_dump($content_dir_mod);
	if(deleteDirectory($content_dir_mod)){
		return true;
	}else{
		return false;
	}
}
?>