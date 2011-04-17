<?php 

/*
 * directory structure:
 * + coscup.org
 * |- 2011 - main website
 * |- 2011-theme - theme
 * |+ deploy - deploy script (cwd)
 *  |- marksite - marksite script
 *  |- src - content source
 *  |- tmp - temporary output
 */

define('TRANSLATE_KEY', 'YOUR_GOOGLE_API_KEY');
define('SPONSOR_LIST_KEY', 'YOUR_GOOGLE_SPREADSHEET_API_KEY_WHICH_HAS_SPONSORS');

define('MARKSITE_PATH', 'marksite/');
define('THEME_PATH', '../2011-theme/');
define('SRC_PATH', 'src/');
define('TMP_PATH', 'tmp/');
define('WEBSITE_PATH', '../2011-beta/');
define('CMS_MODULE_PATH', '../2011-sponsors/modules/coscup_sponsorship/');
define('CMS_THEME_PATH', '../2011-sponsors/sites/all/themes/coscup2011/');
define('SPONSORSHIP_FORM_PATH', 'coscup_sponsorship/');

$sponsors_output = array(
	"sidebar" => array(
		"zh-tw" => "src/blocks/sponsors-zh-tw.md",
		"zh-cn" => "src/blocks/sponsors-zh-cn.md",
		"en" => "src/blocks/sponsors.md"
	),
	"page" => array(
		"zh-tw" => "src/zh-tw/sponsors/index.md",
		"zh-cn" => "src/zh-cn/sponsors/index.md",
		"en" => "src/en/sponsors/index.md"
	)
);

$json_output = array(
	"menu" => "tmp/api/menu/menu.json.js",
	"sponsors" => "src/api/sponsors/sponsors.json.js",
	"program" => "src/api/program/program.json.js"
);



