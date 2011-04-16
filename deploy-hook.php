<?php
include_once("config.php");
include_once("deploy.php");

print ("= Updating Deploy Script =\n");
system ("svn update");
print ("\n");
