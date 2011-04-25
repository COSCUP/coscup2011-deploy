<?php
include_once("deploy.php");

print ("= Reverting Source =\n");
system ("git " . git_cwd(SRC_PATH) . " reset --hard");
print ("\n");

print ("= Updating Source =\n");
system ("git " . git_cwd(SRC_PATH) . " pull origin master");
print ("\n");


print ("= Updating GDoc =\n");
include ("update-gdoc-functions.php");
print ("\n");

recompile_and_sync();
