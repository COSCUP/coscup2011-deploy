<?php
include_once("deploy.php");

print ("= Updating Marksite =\n");
chdir (MARKSITE_PATH);
system ("git " . git_cwd(MARKSITE_PATH) . " pull origin master");
chdir ($cwd);
print ("\n");

recompile_and_sync();
