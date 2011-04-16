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

$marksite_path = "marksite/";
$theme_path = "../2011-theme/";
$src_path = "src/";
$tmp_path = "tmp/";
$website_path = "../2011-beta/";

$sponsors_output_block = "src/blocks/sponsors.md";

$sponsors_output_page = array(
	"zh-tw" => "src/zh-tw/sponsors/index.md",
	"zh-cn" => "src/zh-cn/sponsors/index.md",
	"en" => "src/en/sponsors/index.md"
);

$json_output = array(
	"menu" => "src/api/menu/menu.json",
	"sponsors" => "src/api/sponsors/sponsors.json",
	"program" => "src/api/program/program.json"
);



