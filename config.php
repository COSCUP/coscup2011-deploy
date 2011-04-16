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

$sponsors_output_block = array(
	"zh-tw" => "src/blocks/sponsors-zh-tw.html";
	"zh-cn" => "src/blocks/sponsors-zh-cn.html";
	"en" => "src/blocks/sponsors-en.html";

$sponsors_output_page = array(
	"zh-tw" => "src/zh-tw/sponsors/index.html",
	"zh-cn" => "src/zh-cn/sponsors/index.html",
	"en" => "src/en/sponsors/index.html"
);

$json_output = array(
	"menu" => "src/api/menu/menu.json.js",
	"sponsors" => "src/api/sponsors/sponsors.json.js",
	"program" => "src/api/program/program.json.js"
);



