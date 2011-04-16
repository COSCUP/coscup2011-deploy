<?php
include_once("deploy.php");

print ("= Reverting Source SVN =\n");
system ("svn revert -R " . SRC_PATH);
print ("\n");

print ("= Updating Source SVN =\n");
system ("svn update " . SRC_PATH);
system ("svn info " . SRC_PATH);
print ("\n");


print ("= Updating GDoc =\n");
include ("update-gdoc-functions.php");
print ("\n");

recompile_and_sync();