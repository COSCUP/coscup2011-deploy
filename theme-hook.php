<?php
include_once("deploy.php");

print ("= Updating Theme =\n");
system ("git " . git_cwd(THEME_PATH) . " reset --hard");
system ("git " . git_cwd(THEME_PATH) . " pull origin master");
print ("\n");

print ("= Syncing Theme =\n");
system ('rsync -av --delete ' . THEME_PATH.'drupal/' . ' ' . CMS_THEME_PATH . ' 2>&1');
print ("\n");

recompile_and_sync();
