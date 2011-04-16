<?php
include_once ("deploy.php");

$SPONS = get_sponsors_list_from_gdoc();
foreach ($sponsors_output as $type => $l10n)
{
	foreach ($l10n as $lang => $path)
	{
		$fp = fopen($path, "a");
		fwrite($fp, get_sponsors_html($SPONS, $type, $lang));
		fclose($fp);
	}
}