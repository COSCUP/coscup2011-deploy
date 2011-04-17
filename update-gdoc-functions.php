<?php
include_once ("deploy.php");

function get_sponsors_list_from_gdoc() {

	$handle = @fopen('https://spreadsheets.google.com/pub?key=' . SPONSOR_LIST_KEY . '&range=A2%3AI99&output=csv', 'r');

	if (!$handle)
	{
		return FALSE; // failed
	}

	$SPONS = array();

	// name, level, url, logoUrl, desc, enName, enDesc, zhCnName, zhCnDesc
	while (($SPON = fgetcsv($handle)) !== FALSE)
	{

		$level = $SPON[1];

		if (!isset($SPONS[$level]))
		{
			$SPONS[$level] = array();
		}

		$SPON_obj = array(
			'name' => array(
				'zh-tw' => $SPON[0]
			),
			'desc' => array(
				'zh-tw' => html_pretty($SPON[4])
			),
			'url' => $SPON[2],
			'logoUrl' => $SPON[3],
		);
		
		if (trim($SPON[5]))
		{
			$SPON_obj['name']['en'] = $SPON[5];
		}

		if (trim($SPON[6]))
		{
			$SPON_obj['desc']['en'] = html_pretty($SPON[6]);
		}

		if (trim($SPON[7]))
		{
			$SPON_obj['name']['zh-cn'] = $SPON[7];
		}

		if (trim($SPON[8]))
		{
			$SPON_obj['desc']['zh-cn'] = html_pretty($SPON[8]);
		}

		array_push ($SPONS[$level], $SPON_obj);
	}

	fclose($handle);

	return $SPONS;
}

function get_sponsor_info_localize($SPON, $type='name', $locale='zh-tw', $fallback='zh-tw')
{
	if ($SPON[$type][$locale])
	{
		return $SPON[$type][$locale];
	}
	return $SPON[$type][$fallback];
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
			$html .= sprintf('<ul class="%s">'."\n", $level);

			foreach ($LSPONS as $i => &$SPON)
			{
				$html .= sprintf('<li><a href="%s" target="_blank" title="%s">'.
						 '<img src="%s" width="178" height="72" /></a></li>'."\n",
						htmlspecialchars($SPON['url']),
						htmlspecialchars(get_sponsor_info_localize($SPON, 'name', $lang)),
						htmlspecialchars($SPON['logoUrl'])
						);
			}

			$html .= "</ul>\n";
		}
		break;

		case 'page':
		foreach ($SPONS as $level => &$LSPONS)
		{
			$html .= '<h2>' . htmlspecialchars($levelTitles[$level]) . '</h2>'."\n";

			foreach($LSPONS as $i => &$SPON)
			{

				/* for sponsors who has another logo space */
				if (!trim($SPON['desc']))
				{
					continue;
				}

				$html .= sprintf('<h3><a href="%s" target="_blank">%s</a></h3>'."\n",
						htmlspecialchars($SPON['url']),
						get_sponsor_info_localize($SPON, 'name', $lang)
						);

				$html .= sprintf('<div class="sponsor_content">%s</div>'."\n",
						get_sponsor_info_localize($SPON, 'desc', $lang));

				$html .= "\n";
			}
		}
		break;
	}
	return $html;
}


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

$fp = fopen ($json_output["sponsors"], "w");
fwrite ($fp, json_encode($SPONS));
fclose ($fp);