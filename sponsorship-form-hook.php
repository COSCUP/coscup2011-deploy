<?php
include_once("deploy.php");

print ("= Updating Sponsorship Form =\n");
system ("svn update " . SPONSORSHIP_FORM_PATH);
system ("svn info " . SPONSORSHIP_FORM_PATH);
print ("\n");

print ("= Syncing Sponsorship Form =\n");
system ('rsync -av --delete ' . SPONSORSHIP_FORM_PATH.'drupal/' . ' ' . CMS_MODULE_PATH . ' 2>&1');
print ("\n");
?>
