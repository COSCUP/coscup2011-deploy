<?php 
include_once("config.php");
include_once("google_translate.php");

header('Content-Type: text/plain');
setlocale(LC_ALL, 'en_US.UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') 
{
	die("Error: Not a POST request.");
}

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
	fwrite ($fp, json_encode($marksite->menu));
	fclose ($fp);
	print ("\n");

	print ("= Syncing Content =\n");
	system ('rsync -a --delete ' . TMP_PATH . ' ' . WEBSITE_PATH);
	print ("\n");
}

function html_pretty($string) 
{
	$html = '';
	foreach (explode("\n", htmlspecialchars($string)) as $para) 
	{
		$para = trim($para);
		if (!$para) continue;
		$para = preg_replace('|(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)|', '<a href="$1">$1</a>', $para);
		$para = preg_replace('/(www\.[a-z\.]+)/i', '<a href="http://$1/">$1</a>', $para);
		$para = preg_replace('/([A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]+)/i', '<a href="mailto:$1">$1</a>', $para);
		$html .= '<p>' . $para . '</p>' . "\n";
	}
	return $html;
}

