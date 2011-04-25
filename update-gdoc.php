<?php
include_once ("deploy.php");

print ("= Reverting Source =\n");
system ("git " . git_cwd(SRC_PATH) . " reset --hard");
print ("\n");

if ($_POST["fullupdate"] == 1)
{
	print ("= Updating Marksite =\n");
	system ("git " . git_cwd(MARKSITE_PATH) . " pull origin master");
	print ("\n");


	print ("= Updating Sponsorship Form =\n");
	system ("git " . git_cwd(SPONSORSHIP_FORM_PATH) . " pull origin master");
	print ("\n");

	print ("= Syncing Sponsorship Form =\n");
	system ('rsync -av --delete ' . SPONSORSHIP_FORM_PATH . ' ' . CMS_MODULE_PATH . ' 2>&1');
	print ("\n");


	print ("= Updating Source =\n");
	system ("git " . git_cwd(SRC_PATH) . " pull origin master");
	print ("\n");

	print ("= Updating Theme =\n");
	system ("git " . git_cwd(THEME_PATH) . " reset --hard");
	system ("git " . git_cwd(THEME_PATH) . " pull origin master");
	print ("\n");

	print ("= Syncing Theme =\n");
	system ('rsync -av --delete ' . THEME_PATH.'drupal/' . ' ' . CMS_THEME_PATH . ' 2>&1');
	print ("\n");
}

print ("= Updating GDoc =\n");
include ("update-gdoc-functions.php");
print ("\n");


recompile_and_sync();
