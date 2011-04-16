<?php
include_once("deploy.php");

print ("= Updating Marksite =\n");
system ("svn update " . MARKSITE_PATH);
system ("svn info " . MARKSITE_PATH);
print ("\n");

recompile_and_sync();
