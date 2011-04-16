<?php
include_once("deploy.php");

print ("= Updating Source =\n");
system ("svn update " . SRC_PATH);
system ("svn info " . SRC_PATH);
print ("\n");


print ("= Updating GDoc =\n");
include("update-gdoc.php");

recompile_and_sync();