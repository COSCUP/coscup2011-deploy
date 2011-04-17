<?php
include_once("deploy.php");

print ("= Updating Theme =\n");
system ("svn revert -R " . THEME_PATH);
system ("svn update " . THEME_PATH);
system ("svn info " . THEME_PATH);
print ("\n");

print ("= Syncing Theme =\n");
system ('rsync -av --delete ' . THEME_PATH.'drupal/' . ' ' . CMS_THEME_PATH . ' 2>&1');
print ("\n");

recompile_and_sync();
