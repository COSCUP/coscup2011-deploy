<?php
include_once("deploy.php");

print ("= Updating Source =\n");
system ("svn revert $src_path");
system ("svn update $src_path");
system ("svn info $src_path");
print ("\n");


print ("= Updating GDoc =\n");
include("update-gdoc.php");

recompile_and_sync();