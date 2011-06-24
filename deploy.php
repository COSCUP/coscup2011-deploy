<?php 
include_once("config.php");
include_once("google_translate.php");
include_once(MARKSITE_PATH."markdown.php");

header('Content-Type: text/plain');
setlocale(LC_ALL, 'en_US.UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') 
{
	die("Error: Not a POST request.");
}

$cwd = getcwd();

function recompile_and_sync()
{
	# workaround trying to use value in config.
	include ("config.php");

	print ("= Compiling Content =\n");

	chdir (MARKSITE_PATH);
	include 'marksite.php';
	chdir ("..");
	print ("\n");

	print ("= Writing menu.json.js =\n");
	$fp = fopen ($json_output["menu"], "w");
	$r = array();
	foreach($marksite->menu as $locale => $menuitem)
	{
		$r[$locale] = "<ul>" . $marksite->menu_recursion($menuitem['menu'], 1, 2, false) . "</ul>";
	}
	fwrite ($fp, json_encode($r));
	fclose ($fp);
	print ("\n");

	print ("= Syncing Content =\n");
	system ('rsync -a --delete ' . TMP_PATH . ' ' . WEBSITE_PATH);
	print ("\n");
}

function tag_stripped_markdown($s)
{
	return Markdown(strip_tags($s));
}
