<?php
include_once("config.php");
include_once("deploy.php");

print ("= Updating Deploy Script =\n");
system ("svn revert -R .");
system ("svn update");
system ("svn info");
print ("\n");
