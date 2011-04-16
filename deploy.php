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

function get_sponsors_list_from_gdoc() {

	$handle = @fopen('https://spreadsheets.google.com/pub?key=' . SPONSOR_LIST_KEY . '&range=A2%3AG99&output=csv', 'r');

	if (!$handle)
	{
		return FALSE; // failed
	}

	$SPONS = array();

	// name, level, url, logoUrl, desc, enName, enDesc
	while (($SPON = fgetcsv($handle)) !== FALSE)
	{

		$level = $SPON[1];

		if (!isset($SPONS[$level]))
		{
			$SPONS[$level] = array();
		}

		$SPONS[$level][] = array(
			'name' => array(
				'zh-tw' => html_pretty($SPON[0]),
				'zh-cn' => html_pretty(translate_post($SPON[0])),
				'en' => $SPON[5]
			),
			'desc' => array(
				'zh-tw' => html_pretty($SPON[4]),
				'zh-cn' => html_pretty(translate_post($SPON[4])),
				'en' => $SPON[6]
			),
			'url' => $SPON[2],
			'logoUrl' => $SPON[3],
		);
	}

	fclose($handle);

	return $SPONS;
}

function get_sponsors_html($SPONS, $type = 'sidebar', $lang = 'zh-tw') {

	$levelTitlesL10n = array(
		'en' => array(
			'diamond' => 'Diamond Level Sponsors',
			'gold' => 'Gold Level Sponsors',
			'silver' => 'Silver Level Sponsors',
			'bronze' => 'Bronze Level Sponsors',
			'media' => 'Media Partners'
		),
		'zh-tw' => array(
			'diamond' => '鑽石級贊助商',
			'gold' => '黃金級贊助商',
			'silver' => '白銀級贊助商',
			'bronze' => '青銅級贊助商',
			'media' => '媒體夥伴'
		),
		'zh-cn' => array(
			'diamond' => '钻石级赞助商',
                        'gold' => '黄金级赞助商',
                        'silver' => '白银级赞助商',
                        'bronze' => '青铜级赞助商',
                        'media' => '媒体伙伴'
		)
	);

	$levelTitles = $levelTitlesL10n[$lang];

	$html = '';
	switch ($type)
	{
		case 'sidebar':
		foreach ($SPONS as $level => &$LSPONS)
		{
			$html .= sprintf("<h2>%s</h2>\n", htmlspecialchars($levelTitles[$level]));
			$html .= "<ul>\n";

			foreach ($LSPONS as $i => &$SPON)
			{
				$html .= sprintf('<li><a href="%s" target="_blank" title="%s">'.
						 '<img src="%s" width="178" height="72" /></a></li>'."\n",
						htmlspecialchars($SPON['url']),
						htmlspecialchars($SPON['name'][$lang]),
						htmlspecialchars($SPON['logoUrl'])
						);
			}

			$html .= "</ul>\n";
		}
		break;

		case 'page':
		foreach ($SPONS as $level => &$LSPONS)
		{
			$html .= '<h2>' . htmlspecialchars($levelTitles[$level]) . '</h2>\n';

			foreach($LSPONS as $i => &$SPON)
			{

				/* for sponsors who has another logo space */
				if (!trim($SPON['desc']))
				{
					continue;
				}

				$html .= sprintf('<h3><a href="%s" target="_blank">%s</a></h3>'."\n",
						htmlspecialchars($SPON['url']),
						htmlspecialchars($SPON['name'][$lang])
						);

				$html .= sprintf('<div class="sponsor_content">%s</div>'."\n",
						$SPON['desc'][$lang]);

				$html .= "\n";
			}
		}
		break;
	}
	return $html;
}

function html_pretty($string) {
	$html = '';
	foreach (explode("\n", htmlspecialchars($string)) as $para) {
		$para = trim($para);
		if (!$para) continue;
		$para = preg_replace('|(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)|', '<a href="$1">$1</a>', $para);
		$para = preg_replace('/(www\.[a-z\.]+)/i', '<a href="http://$1/">$1</a>', $para);
		$para = preg_replace('/([A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]+)/i', '<a href="mailto:$1">$1</a>', $para);
		$html .= '<p>' . $para . '</p>' . "\n";
	}
	return $html;
}
