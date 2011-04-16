<?php
include_once("deploy.php");

print ("= Updating Source =\n");
system ("svn update $src_path");
system ("svn info $src_path");
print ("\n");

recompile_and_sync();
