<?php
include_once("deploy.php");

print ("= Updating Marksite =\n");
system ("svn update $marksite_path");
system ("svn info $marksite_path");
print ("\n");

recompile_and_sync();
