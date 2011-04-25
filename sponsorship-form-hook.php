<?php
include_once("deploy.php");

print ("= Updating Sponsorship Form =\n");
system ("git " . git_cwd(SPONSORSHIP_FORM_PATH) . " pull origin master");
print ("\n");

print ("= Syncing Sponsorship Form =\n");
system ('rsync -av --delete ' . SPONSORSHIP_FORM_PATH . ' ' . CMS_MODULE_PATH . ' 2>&1');
print ("\n");
?>
