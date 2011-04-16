<?php 
include_once("config.php");
header('Content-Type: text/plain');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') 
{
	die("Error: Not a POST request.");
}

function recompile_and_sync()
{
	print ("= Compiling Content =\n");
	# system("php marksite.php") isn't possible on fatcow
	chdir ("marksite");
	include 'marksite.php';
	chdir ("..");
	print ("\n");

	print ("= Syncing Content =\n");
	system ("rsync -a --delete $tmp_path $website_path");
	print ("\n");
}
