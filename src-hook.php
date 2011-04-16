<?php
include_once("deploy.php");
include_once ("update-gdoc-functions.php");

print ("= Reverting Source SVN =\n");
system ("svn revert -R " . SRC_PATH);
print ("\n");

print ("= Updating Source SVN =\n");
system ("svn update " . SRC_PATH);
system ("svn info " . SRC_PATH);
print ("\n");


print ("= Updating GDoc =\n");
update_gdoc();
print ("\n");

recompile_and_sync();