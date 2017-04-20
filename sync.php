<?php
echo "<h1>MANUAL database synchronization: ";
$rc=system("/usr/local/bin/sudo /usr/local/www/clone_users_table.sh");
if ($rc==0) { echo "SUCCEEDED"; }
else
{ echo "FAILED";}
?>
