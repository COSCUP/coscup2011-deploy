<?php
include_once("deploy.php");

print ("= Updating Marksite =\n");
system ("git " . git_cwd(MARKSITE_PATH) . " pull origin master");
print ("\n");

recompile_and_sync();
