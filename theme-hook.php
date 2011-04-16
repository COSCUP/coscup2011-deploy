<?php
include_once("deploy.php");

print ("= Updating Theme =\n");
system ("svn update " . THEME_PATH);
system ("svn info " . THEME_PATH);
print ("\n");

recompile_and_sync();
