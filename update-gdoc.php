<?php
include_once ("deploy.php");

print ("= Reverting Source SVN =\n");
system ("svn revert -R " . SRC_PATH);
print ("\n");

if ($_POST["fullupdate"] == 1)
{
	print ("= Updating Marksite =\n");
	system ("svn update " . MARKSITE_PATH);
	system ("svn info " . MARKSITE_PATH);
	print ("\n");


	print ("= Updating Sponsorship Form =\n");
	system ("svn update " . SPONSORSHIP_FORM_PATH);
	system ("svn info " . SPONSORSHIP_FORM_PATH);
	print ("\n");

	print ("= Syncing Sponsorship Form =\n");
	system ('rsync -av --delete ' . SPONSORSHIP_FORM_PATH . ' ' . CMS_MODULE_PATH . ' 2>&1');
	print ("\n");


	print ("= Updating Source SVN =\n");
	system ("svn update " . SRC_PATH);
	system ("svn info " . SRC_PATH);
	print ("\n");

	print ("= Updating Theme =\n");
	system ("svn revert -R " . THEME_PATH);
	system ("svn update " . THEME_PATH);
	system ("svn info " . THEME_PATH);
	print ("\n");

	print ("= Syncing Theme =\n");
	system ('rsync -av --delete ' . THEME_PATH.'drupal/' . ' ' . CMS_THEME_PATH . ' 2>&1');
	print ("\n");
}

print ("= Updating GDoc =\n");
include ("update-gdoc-functions.php");
print ("\n");


recompile_and_sync();