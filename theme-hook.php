<?php
include_once("deploy.php");

print ("= Updating Theme =\n");
system ("svn update $theme_path");
system ("svn info $theme_path");
print ("\n");

recompile_and_sync();
