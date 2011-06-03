<?php
include_once ("deploy.php");

date_default_timezone_set('Asia/Taipei');
setlocale (LC_ALL, "en_US.UTF-8");

function get_program_list_from_gdoc() {

	$handle = @fopen('https://spreadsheets.google.com/pub?key=' . PROGRAM_LIST_KEY . '&range=A2%3AJ99&output=csv', 'r');

	if (!$handle)
	{
		return FALSE; // failed
	}

	$program_list = array();

	// name, from, to, room, type, speaker, speakerTitle, desc, language
	while (($program = fgetcsv($handle)) !== FALSE)
	{

		$program_obj = array(
			'name' => $program[0],

			// use strtotime to convert to unix timestamp.
			'from' => strtotime($program[1]),
			'to' => strtotime($program[2]),

			'room' => intval($program[3])
		);

		if (trim($program[4]))
		{
			$program_obj['type'] = intval($program[4]);
		}

		if (trim($program[5]))
		{
			$program_obj['speaker'] = $program[5];

			if (trim($program[6]))
			{
				$program_obj['speakerTitle'] = $program[6];
			}
			if (trim($program[7]))
			{
				$program_obj['bio'] = html_pretty($program[7]);
			}
		}

		if (trim($program[8]))
		{
			$program_obj['abstract'] = html_pretty($program[8]);
		}

		if (trim($program[9]))
		{
			$program_obj['lang'] = $program[9];
		}

		$program_list[] = $program_obj;
	}

	fclose($handle);

	return $program_list;
}

function get_program_types_from_gdoc() {

	// TODO: constant gid written in uri
	$handle = @fopen('https://spreadsheets.google.com/pub?key=' . PROGRAM_LIST_KEY . '&gid=3&range=A2%3AB99&output=csv', 'r');

	if (!$handle)
	{
		return FALSE; // failed
	}

	$type_list = array();

	// id, name
	while (($type = fgetcsv($handle)) !== FALSE)
	{
		$type_list[intval($type[0])] = $type[1];
	}

	fclose($handle);

	return $type_list;
}

function get_program_rooms_from_gdoc() {

	// TODO: constant gid written in uri
	$handle = @fopen('https://spreadsheets.google.com/pub?key=' . PROGRAM_LIST_KEY . '&gid=4&range=A2%3AD99&output=csv', 'r');

	if (!$handle)
	{
		return FALSE; // failed
	}

	$room_list = array();

	// id, name, nameEn, nameZhCn
	while (($room = fgetcsv($handle)) !== FALSE)
	{
		$room_list[intval($room[0])] = array(
			'zh-tw' => $room[1],
			'en' => $room[2],
			'zh-cn' => $room[3]
		);
	}

	fclose($handle);

	return $room_list;
}

function get_program_list_html(&$program_list, &$type_list, &$room_list, $lang = 'zh-tw') {

	$l10n = array(
		'en' => array(
			'time' => 'Time',
			'day_1' => 'Day 1',
			'day_2' => 'Day 2'
		),
		'zh-tw' => array(
			'time' => '時間',
			'day_1' => '第一天',
			'day_2' => '第二天'
		),
		'zh-cn' => array(
			'time' => '时间',
			'day_1' => '第一天',
			'day_2' => '第二天'
		)
	);




	// constructing data structures

	$structure = array();
	$time_structure = array();


	foreach ($program_list as $id => &$program)
	{

		$program['id'] = $id;

		if(!isset($structure[$program['from']]))
		{
			$structure[$program['from']] = array();
		}

		$structure[$program['from']][$program['room']] =& $program;
		$time_structure[] = $program['from'];
		$time_structure[] = $program['to'];
	}

	$time_structure = array_unique($time_structure);
	sort($time_structure);


	





	

	$html = '';

	$html .= '<ul class="shortcuts">';

	foreach (array(1, 2) as $day) {
		$html .= sprintf('<li><a href="#day%d">%s</a></li>'."\n",
				$day,
				$l10n[$lang]["day_$day"]
				);
	}

	$html .= '</ul>' . "\n\n";

	$html .= '<ul class="types">';
	foreach($type_list as $type_id => $type_name)
	{
		if ($type_id <= 0)
		{
			continue;
		}
		$html .= sprintf('<li class="program_type_%d">%s</li>'."\n",
				$type_id,
				htmlspecialchars($type_name)
				);
	}
	$html .= '</ul>' . "\n\n";








	$last_stamp = 0;
	$day_increment = 0;

	foreach ($time_structure as $time_id => $time_stamp)
	{
		if (!isset($structure[$time_stamp]))
		{
			continue;
		}

		$last_time = getdate($last_stamp);
		$this_time = getdate($time_stamp);
		$this_time_formatted = strftime("%R", $time_stamp);
		$to_time_formatted = strftime("%R", $time_structure[$time_id+1]);
		
		if ($last_time['yday'] != $this_time['yday'] || $last_time['year'] != $this_time['year'])
		{
			if($day_increment > 0)
			{
				$html .= '</tbody></table>'."\n";
			}
			$day_increment += 1;
			$html .= '<h2 id="day' . $day_increment . '">'
				. $l10n[$lang]["day_$day_increment"] 
				. ' (' . $this_time['mon'] . '/' . $this_time['mday'] . ')' 
				. '</h2>'
				."\n";

			$html .= <<<EOT
<table class="program">
<thead>
	<tr><th>{$l10n[$lang]['time']}</th>

EOT;

			foreach($room_list as $k => $v)
			{
				if ($k <= 0)
				{
					continue;
				}

				$html .= "<th>$v[$lang]</th>";
			}

			$html .= <<<EOT
	</tr>
</thead>
<tbody>

EOT;

		}



		$html .= <<<EOT
	<tr>
		<th><span>{$this_time_formatted}</span> — {$to_time_formatted}</th>

EOT;

		ksort($structure[$time_stamp]);
		foreach ($structure[$time_stamp] as &$program)
		{
			// calculate colspan and rowspan
			$colspan = $program['room'] === 0 ? sizeof($room_list)-1 : 1;
			
			$rowspan = 1;
			while ($time_structure[$time_id + $rowspan] < $program['to'])
			{
				$rowspan += 1;
			}




			// build classlist
			$class_list = array();
			$class_list[] = "program_content";

			if (isset($program['lang']))
			{
				$class_list[] = "program_lang_{$program['lang']}";
			}

			if (isset($program['type']))
			{
				$class_list[] = "program_type_{$program['type']}";
			}

			if (isset($program['room'])) // FIXME: some events doesn't need room information.
			{
				$class_list[] = "program_room_{$program['room']}";
			}

			$class_list_string = implode(" ", $class_list);

			$html .= <<<EOT
		<td data-pid="{$program['id']}" class="{$class_list_string}" colspan="{$colspan}" rowspan="{$rowspan}">
EOT;



			$html .= '<p class="name">'.htmlspecialchars($program['name']).'</p>';



			if (isset($program['room'])) // FIXME: some events doesn't need room information.
			{
				$html .= '<p class="room">' . htmlspecialchars($room_list[$program['room']][$lang]) . '</p>';
			}

			if (isset($program['speaker']))
			{
				$html .= '<p class="speaker">' . htmlspecialchars($program['speaker']) . '</p>';

				if (isset($program['speakerTitle']))
				{
					$html .= '<p class="speakerTitle">' . htmlspecialchars($program['speakerTitle']) . '</p>';
				}
			}



			$html .= "</td>\n";
		}

		$html .= "</tr>\n\n";

		$last_stamp = $time_stamp;
	}

	$html .= '</tbody></table>'."\n";

	return $html;
}

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
			'diamond' => '鑽石級贊助',
			'gold' => '黃金級贊助',
			'silver' => '白銀級贊助',
			'bronze' => '青銅級贊助',
			'media' => '媒體夥伴'
		),
		'zh-cn' => array(
			'diamond' => '钻石级赞助商',
                        'gold' => '黄金级赞助',
                        'silver' => '白银级赞助',
                        'bronze' => '青铜级赞助',
                        'media' => '媒体伙伴'
		)
	);

	// order of levels (fixed)
	$levels = array(
		'diamond',
		'gold',
		'silver',
		'bronze',
		'media'
	);

	$levelTitles = $levelTitlesL10n[$lang];

	$html = '';
	switch ($type)
	{
		case 'sidebar':
		foreach ($levels as &$level)
		{
			if (!$SPONS[$level]) continue;

			$html .= sprintf("<h2>%s</h2>\n", htmlspecialchars($levelTitles[$level]));
			$html .= sprintf('<ul class="%s">'."\n", $level);

			foreach ($SPONS[$level] as $i => &$SPON)
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
		$html .= '<div class="sponsors">';
		foreach ($levels as &$level)
		{
			if (!$SPONS[$level]) continue;

			$html .= '<h2>' . htmlspecialchars($levelTitles[$level]) . '</h2>'."\n";

			foreach ($SPONS[$level] as $i => &$SPON)
			{

				/* for sponsors who has another logo space, exclude media partners */
				if ($level !== 'media' && !trim(get_sponsor_info_localize($SPON, 'desc', $lang)))
				{
					continue;
				}

				$html .= sprintf('<h3><a href="%s" target="_blank">%s</a></h3>'."\n",
						htmlspecialchars($SPON['url']),
						get_sponsor_info_localize($SPON, 'name', $lang)
						);

				if (trim(get_sponsor_info_localize($SPON, 'desc', $lang)))
				{
					$html .= sprintf('<div class="sponsor_content">%s</div>'."\n",
							get_sponsor_info_localize($SPON, 'desc', $lang));
				}

				$html .= "\n";
			}
		}
		$html .= '</div>';
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




$program_list = get_program_list_from_gdoc();
$program_types_list = get_program_types_from_gdoc();
$program_rooms_list = get_program_rooms_from_gdoc();

foreach ($program_list_output as $lang => $path)
{
	$fp = fopen($path, "a");
	fwrite($fp, get_program_list_html($program_list, $program_types_list, $program_rooms_list, $lang));
	fclose($fp);
}

$fp = fopen ($json_output["program_list"], "w");
fwrite ($fp, json_encode($program_list));
fclose ($fp);

$fp = fopen ($json_output["program_types"], "w");
fwrite ($fp, json_encode($program_types_list));
fclose ($fp);

$fp = fopen ($json_output["program_rooms"], "w");
fwrite ($fp, json_encode($program_rooms_list));
fclose ($fp);
