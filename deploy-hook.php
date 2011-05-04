<?php
include_once("config.php");
include_once("deploy.php");

print ("= Updating Deploy Script =\n");
system ("git reset --hard");
system ("git pull origin git");
print ("\n");
