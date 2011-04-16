<?php 
include_once("config.php");
include_once("google_translate.php");

header('Content-Type: text/plain');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') 
{
	die("Error: Not a POST request.");
}

function recompile_and_sync()
{
	print ("= Compiling Content =\n");

	chdir (MARKSITE_PATH);
	include 'marksite.php';
	chdir ("..");
	print ("\n");

	print ("= Syncing Content =\n");
	system ('rsync -a --delete ' . TMP_PATH . ' ' . WEBSITE_PATH);
	print ("\n");
}
